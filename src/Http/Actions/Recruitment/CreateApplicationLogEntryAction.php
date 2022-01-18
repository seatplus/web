<?php

namespace Seatplus\Web\Http\Actions\Recruitment;


use Seatplus\Auth\Models\User;
use Seatplus\Eveapi\Models\Recruitment\ApplicationLogs;

class CreateApplicationLogEntryAction
{

    private string $application_id;

    private string $comment = '';

    private string $type;

    public function getApplicationId(): string
    {
        return $this->application_id;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): CreateApplicationLogEntryAction
    {

        throw_unless(in_array($type, ['comment', 'decision']), 404, 'type must be comment or decision');

        $this->type = $type;
        return $this;
    }

    public function setApplicationId(string $application_id) : self
    {
        $this->application_id = $application_id;

        return $this;
    }

    public function setComment(string $comment) : self
    {
        $this->comment = $comment;

        return $this;
    }

    public function execute()
    {

        ApplicationLogs::query()->create([
            'application_id' => $this->getApplicationId(),
            'causer_type' => User::class,
            'causer_id' => auth()->user()->getAuthIdentifier(),
            'type' => $this->getType(),
            'comment' => $this->getComment(),
        ]);

    }

}