<?php

/*
 * MIT License
 *
 * Copyright (c) 2019, 2020, 2021 Felix Huber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

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

    public function setApplicationId(string $application_id): self
    {
        $this->application_id = $application_id;

        return $this;
    }

    public function setComment(string $comment): self
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
