<?php

namespace Cargonomica\Qunique;

class Job
{
    private array $jobData;

    public function __construct(array $jobData = [])
    {
        if (empty($jobData)) {
            $this->jobData = [
                'id' => uniqid('', true),
                'title' => '',
                'tags' => [],
                'class' => '',
                'args' => [],
                'tries' => 1,
                'delay' => 0,
                'errors' => [],
                'createdAt' => time()
            ];
        } else {
            $this->jobData = $jobData;
        }
    }

    public function setTitle(string $title): self
    {
        $this->jobData['title'] = $title;
        return $this;
    }

    public function setClass(string $class): self
    {
        $this->jobData['class'] = $class;
        return $this;
    }

    public function setArgs(array $args): self
    {
        $this->jobData['args'] = $args;
        return $this;
    }

    public function setTags(array $tags): self
    {
        $this->jobData['tags'] = $tags;
        return $this;
    }

    public function setTries(int $tries): self
    {
        $this->jobData['tries'] = $tries;
        return $this;
    }

    public function setDelay(int $delay): self
    {
        $this->jobData['delay'] = $delay;
        return $this;
    }

    public function getJobData(): array
    {
        return $this->jobData;
    }

    public function getId(): string
    {
        return $this->jobData['id'];
    }

    public function getTitle(): string
    {
        return $this->jobData['title'];
    }

    public function getTags(): array
    {
        return $this->jobData['tags'];
    }

    public function getClass(): string
    {
        return $this->jobData['class'];
    }

    public function getArgs(): array
    {
        return $this->jobData['args'];
    }

    public function getTries(): int
    {
        return $this->jobData['tries'];
    }

    public function getDelay(): int
    {
        return $this->jobData['delay'];
    }

    public function getErrors(): array
    {
        return $this->jobData['errors'];
    }

    public function getCreatedAt(): int
    {
        return $this->jobData['createdAt'];
    }

    public function decrementTries(): void
    {
        $this->jobData['tries']--;
    }

    public function addTag(string $tag): void
    {
        $this->jobData['tags'][] = $tag;
    }

    public function addError(array $error): void
    {
        $this->jobData['errors'][] = $error;
    }

    public function execute(): void
    {
        $class = $this->jobData['class'];
        $object = new $class(...$this->jobData['args']);
        $object->execute();
    }
}