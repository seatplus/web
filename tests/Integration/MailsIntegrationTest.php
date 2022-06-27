<?php


use Illuminate\Support\Facades\Event;
use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\AssertableInertia as Assert;
use Seatplus\Auth\Models\Permissions\Permission;
use Seatplus\EsiClient\DataTransferObjects\EsiResponse;
use Seatplus\Eveapi\Models\Character\CharacterInfo;
use Seatplus\Eveapi\Models\Mail\Mail;
use Seatplus\Eveapi\Models\Mail\MailRecipients;
use Seatplus\Eveapi\Services\Facade\RetrieveEsiData;
use Spatie\Permission\PermissionRegistrar;
use function Pest\Laravel\get;

test('see component', function () {
    $response = test()->actingAs(test()->test_user)
        ->get(route('character.mails'));

    $response->assertInertia(fn (Assert $page) => $page->component('Character/Mail/Index'));
});

test('get mail headers of secondary user', function () {
    if (test()->test_user->can('superuser')) {
        test()->test_user->removeRole('superuser');

        // now re-register all the roles and permissions
        app()->make(PermissionRegistrar::class)->registerPermissions();
    }

    $response = test()->actingAs(test()->test_user)
        ->get(route('get.mail.headers', ['character_ids' => [test()->test_character->character_id]]));

    $response->assertOk();
});

test('get mail body test', function () {
    $body = '<font size="12" color="#bfffffff"></font><font size="12" color="#ff007fff">hey man hope you change your mind and come back to us<br><br>if not enjoy and maybe paths will cross again one day<br></font><font size="12" color="#bfffffff">--------------------------------<br>Re: FW: Leaving<br>From: </font><font size="12" color="#ffffa600"><a href="showinfo:1377//91356804">Steel Roamer</a><br></font><font size="12" color="#bfffffff">Sent: 2018.02.08 17:42<br>To: ShekelSquad, </font><font size="12" color="#ffffa600"><a href="showinfo:1377//95002093">Rory Wolf</a></font><font size="12" color="#bfffffff">,  <br><br>:(<br><br>--------------------------------<br>FW: Leaving<br>From: Rory Wolf<br>Sent: 2018.02.08 17:28<br>To: ShekelSquad<br><br></font><font size="12" color="#ff007fff">OK which one of you guys has the shitty attitude?<br><br>Possible answers:<br><br>NOT ME!<br>Who cares!<br>Everyone!<br>SteelRoamer!!!!!!!!!!!!!!!!!!!!!!<br><br></font><font size="12" color="#bfffffff">--------------------------------<br>Leaving<br>From: </font><font size="12" color="#ffffa600"><a href="showinfo:1377//94159646">evillady Lennelluc</a><br></font><font size="12" color="#bfffffff">Sent: 2018.02.08 13:51<br>To: ShekelSquad, <br><br></font><font size="12" color="#ff007fff">hey guys<br><br>i desided to leave corp <br>mostly cause of lack of people in my TZ <br>also some peoples additude <br>i wish you guys good luck and lost of fun <br>and maybe untill we meet again o/<br><br>fly it like yo stole it o7<br>Evil</font>';

    $mail = Mail::factory()->create([
        'from' => 96_898_138,
        'body' => $body,
    ]);

    $secondary_charcter = Event::fakeFor(fn () => CharacterInfo::factory()->create());

    $mail_receipient = Event::fakeFor(fn () => MailRecipients::factory()->create([
        'mail_id' => $mail->id,
        'receivable_id' => $secondary_charcter->character_id,
        'receivable_type' => CharacterInfo::class,
    ]));


    //Prepare ESI Response of GetIdsFromNamesService
    $data = [
        [
            "id" => 91_356_804,
            "name" => "Steel Roamer",
        ],
        [
            "id" => 98_467_521,
            "name" => "ShekelSquad",
        ],
        [
            'id' => 95_002_093,
            "name" => "Rory Wolf",
        ],
        [
            'id' => 94_159_646,
            "name" => "evillady Lennelluc",
        ],
    ];

    //Mock EsiResponse
    $response = new EsiResponse(json_encode($data), [], 'now', 200);

    RetrieveEsiData::shouldReceive('execute')
        ->once()
        ->andReturn($response);

    // Give user superuser
    test()->assignPermissionToTestUser('superuser');

    expect(test()->test_user->can('superuser'))->toBeTrue();

    expect(Mail::all())->toHaveCount(1);

    test()->actingAs(test()->test_user);

    $response = get(route('get.mail', $mail->id));

    $response->assertJson(fn (AssertableJson $json) => $json->count(4));

});
