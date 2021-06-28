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

namespace Seatplus\Web\Services\Mails;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Web\Services\GetIdsFromNamesService;

class EveMailService
{
    private \Illuminate\Support\Collection $namesToResolve;

    public function __construct(private Mail $mail)
    {
        $this->namesToResolve = collect();
    }

    public static function make(Mail $mail)
    {
        return new static($mail);
    }

    public function getThreads(): Collection
    {
        $clean_body = $this->stripTags($this->mail->body ?? 'mail body has not been fetched yet.');

        //now split the clean body into threads
        return collect(explode('--------------------------------<br>', $clean_body))
            ->map(fn ($mail, $index) => $index === 0 ? $this->handleFirstMail($mail) : $this->handleMail($mail))
            ->pipe(function (Collection $collection) {
                $resolved_names = $this->resolveNames();

                return $collection->map(fn ($mail, $index) => $index === 0 ? $mail : $this->enrichMail($mail, $resolved_names));
            });
    }

    private function stripTags(string $message): string
    {
        $body = mb_convert_encoding($message, 'HTML-ENTITIES', 'UTF-8');

        return strip_tags($body, '<br>');
    }

    private function handleFirstMail(string $mail)
    {
        return [
            'from' => ['id' => $this->mail->from],
            'recipients' => $this->mail->recipients->map(fn ($recipient) => ['id' => $recipient->receivable_id]),
            'timestamp' => carbon($this->mail->timestamp),
            'labels' => $this->mapLabels($this->mail->labels),
            'subject' => $this->mail->subject,
            'body' => $mail,
        ];
    }

    private function handleMail(string $mail)
    {
        $mail = $this->stripTags($mail);

        $message = explode('<br>', $mail, 5);

        return [
            'from' => $this->getSender($message[1]),
            'recipients' => $this->getRecipients($message[3]),
            'timestamp' => Carbon::createFromFormat('Y.m.d H:i', Str::after($message[2], 'Sent: ')),
            'subject' => $message[0],
            'body' => $message[4],
        ];
    }

    private function getSender(string $sender): string
    {
        $sender = Str::after($sender, 'From: ');

        $this->namesToResolve->push($sender);

        return $sender;
    }

    private function getRecipients(string $recipients): array
    {
        $recipients = explode(', ', Str::beforeLast(Str::after($recipients, 'To: '), ','));

        $this->namesToResolve->push(...$recipients);

        return $recipients;
    }

    private function resolveNames()
    {
        return GetIdsFromNamesService::make()->execute($this->namesToResolve->flatten()->unique()->toArray());
    }

    private function enrichMail(array $mail, Collection $resolved_names)
    {
        $mail['from'] = $resolved_names->firstWhere('name', $mail['from']);

        $mail['recipients'] = collect($mail['recipients'])
            ->map(fn (string $recipient) => (array) $resolved_names->firstWhere('name', $recipient))
            ->toArray();

        return $mail;
    }

    private function mapLabels(Collection $collection)
    {
        return $collection->map(fn ($label) => [
            'name' => $label->name,
            'color' => $label->color,
        ]);
    }

    private function mapColors(string $hexcode): string
    {
        return match ($hexcode) {
            '#0000fe' => 'bg-indigo-100 text-indigo-800',
            '#006634'  => 'bg-emerald-100 text-emerald-800',
            '#0099ff' => 'bg-blue-100 text-blue-800',
            '#00ff33' => 'bg-lime-100 text-lime-800',
            '#01ffff' => 'bg-sky-100 text-sky-800',
            '#349800' => 'bg-green-100 text-green-800',
            '#660066' => 'bg-fuchsia-100 text-fuchsia-800',
            '#666666', 'default' => 'bg-gray-100 text-gray-800',
            '#999999' => 'bg-warmGray-100 text-warmGray-800',
            '#99ffff' => 'bg-cyan-100 text-cyan-800',
            '#9a0000' => 'bg-rose-100 text-rose-800',
            '#ccff9a' => 'bg-teal-100 text-teal-800',
            '#e6e6e6' => 'bg-coolGray-100 text-coolGray-800',
            '#fe0000' => 'bg-red-100 text-red-800',
            '#ff6600' => 'bg-orange-100 text-orange-800',
            '#ffff01' => 'bg-yellow-100 text-yellow-800',
            '#ffffcd' => 'bg-trueGray-100 text-trueGray-800',
            '#ffffff' => 'bg-blueGray-100 text-blueGray-800'
        };
    }
}
