<?php

namespace Qunique;

use Predis\Client;
use Throwable;

class Queue
{
    protected Client $redis;
    protected string $queueName;
    protected string $failedQueueName;

    public function __construct(string $queueName = 'default_queue')
    {
        $this->redis = new Client();
        $this->queueName = $queueName;
        $this->failedQueueName = $queueName . '_failed';
    }

    public function pushJob(Job $job): string
    {
        $jobData = $job->getJobData();
        $this->redis->rpush($this->queueName, (array)json_encode($jobData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        foreach ($job->getTags() as $tag) {
            $this->redis->sadd('all_tags', $tag);
            $this->redis->sadd("tag:$tag", $jobData['id']);
        }
        return $jobData['id'];
    }

    public function handleJob(): ?array
    {
        $data = $this->redis->lpop($this->queueName);
        if ($data) {
            $jobData = json_decode($data, true);
            $job = new Job($jobData);
            $currentTime = time();

            if ($currentTime - $job->getCreatedAt() < $job->getDelay()) {
                $this->redis->rpush($this->queueName, (array)json_encode($jobData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                return null;
            }

            try {
                $job->execute();
                return ['id' => $job->getId(), 'status' => 'success'];
            } catch (Throwable $e) {
                $this->handleJobError($job, $e);
            }

            if ($job->getTries() > 1) {
                $job->decrementTries();
                $this->redis->rpush($this->queueName, (array)json_encode($job->getJobData(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            } else {
                $this->redis->rpush($this->failedQueueName, (array)json_encode($job->getJobData(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                return ['id' => $job->getId(), 'status' => 'failed', 'errors' => $job->getErrors()];
            }
        }
        return null;
    }

    private function handleJobError(Job $job, Throwable $e): void
    {
        $job->addError([
            "type" => get_class($e),
            "message" => $e->getMessage(),
            "time" => time(),
            "tries" => $job->getTries(),
        ]);
    }

    public function getFailedJobs(): array
    {
        $failedJobs = $this->redis->lrange($this->failedQueueName, 0, -1);
        return array_map(function ($job) {
            return json_decode($job, true);
        }, $failedJobs);
    }

    public function retryFailedJobs(): void
    {
        $failedJobs = $this->getFailedJobs();
        foreach ($failedJobs as $jobData) {
            $job = new Job($jobData);
            $job->addTag("retryFailed");
            $this->pushJob($job);
        }
        $this->redis->del($this->failedQueueName);
    }

    public function getJobs(int $page = 0, int $limit = 500, array $tags = []): string
    {
        $offset = $page * $limit;

        if (!empty($tags)) {
            $jobIds = [];
            foreach ($tags as $tag) {
                $tagJobIds = $this->redis->smembers("tag:$tag");
                $jobIds = array_merge($jobIds, $tagJobIds);
            }
            $jobIds = array_unique($jobIds);
            sort($jobIds);
        } else {
            $jobIds = null;
        }

        $jobs = $this->redis->lrange($this->queueName, $offset, $offset + $limit - 1);

        if ($jobIds !== null) {
            $jobs = array_filter($jobs, function ($job) use ($jobIds) {
                $jobData = json_decode($job, true);
                return in_array($jobData['id'], $jobIds);
            });
        }
        $total = $this->redis->llen($this->queueName);

        $data = "[" . join(",", $jobs) . "]";
        return '{"type":"jobs","page":' . $page . ',"limit":' . $limit . ',"total":' . $total . ',"data":' . $data . '}';
    }

    public function clearJobsQueue(): void
    {
        $this->redis->del($this->queueName);
    }

    public function clearFailedJobsQueue(): void
    {
        $this->redis->del($this->failedQueueName);
    }

    public function getAllTags(): array
    {
        return $this->redis->smembers('all_tags');
    }
}
