<?php

use Illuminate\Support\Facades\Event;
use Seatplus\EsiClient\EsiClient;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Eveapi\Models\Mail\MailRecipients;
use Seatplus\Web\Services\Mails\EveMailService;

it('builds mail threads and resolves names via esi', function () {
    $body = '<font size="12" color="#bfffffff"></font><font size="12" color="#ff007fff">hey man hope you change your mind and come back to us<br><br>if not enjoy and maybe paths will cross again one day<br></font><font size="12" color="#bfffffff">--------------------------------<br>Re: FW: Leaving<br>From: </font><font size="12" color="#ffffa600"><a href="showinfo:1377//91356804">Steel Roamer</a><br></font><font size="12" color="#bfffffff">Sent: 2018.02.08 17:42<br>To: ShekelSquad, </font><font size="12" color="#ffffa600"><a href="showinfo:1377//95002093">Rory Wolf</a></font><font size="12" color="#bfffffff">,  <br><br>:(<br><br>--------------------------------<br>FW: Leaving<br>From: Rory Wolf<br>Sent: 2018.02.08 17:28<br>To: ShekelSquad<br><br></font><font size="12" color="#ff007fff">OK which one of you guys has the shitty attitude?<br><br>Possible answers:<br><br>NOT ME!<br>Who cares!<br>Everyone!<br>SteelRoamer!!!!!!!!!!!!!!!!!!!!!!<br><br></font><font size="12" color="#bfffffff">--------------------------------<br>Leaving<br>From: </font><font size="12" color="#ffffa600"><a href="showinfo:1377//94159646">evillady Lennelluc</a><br></font><font size="12" color="#bfffffff">Sent: 2018.02.08 13:51<br>To: ShekelSquad, <br><br></font><font size="12" color="#ff007fff">hey guys<br><br>i desided to leave corp <br>mostly cause of lack of people in my TZ <br>also some peoples additude <br>i wish you guys good luck and lost of fun <br>and maybe untill we meet again o/<br><br>fly it like yo stole it o7<br>Evil</font>';

    $mail = Mail::factory()->create([
        'from' => 96_898_138,
        'body' => $body,
    ]);

    $secondary_character = Event::fakeFor(fn () => CharacterInfo::factory()->create());

    Event::fakeFor(fn () => MailRecipients::factory()->create([
        'mail_id' => $mail->id,
        'receivable_id' => $secondary_character->character_id,
        'receivable_type' => CharacterInfo::class,
    ]));

    $esi = Mockery::mock(EsiClient::class);
    mockEsiTransport($esi, makeEsiResult([
        (object) ['id' => 91_356_804, 'name' => 'Steel Roamer'],
        (object) ['id' => 98_467_521, 'name' => 'ShekelSquad'],
        (object) ['id' => 95_002_093, 'name' => 'Rory Wolf'],
        (object) ['id' => 94_159_646, 'name' => 'evillady Lennelluc'],
    ]));

    $mail = Mail::query()->with('recipients')->find($mail->id);

    $threads = EveMailService::make($mail)->getThreads($esi);

    expect($threads)->toHaveCount(4);
});
