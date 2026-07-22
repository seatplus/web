<?php

use Seatplus\Eveapi\Models\Universe\Category;
use Seatplus\Eveapi\Models\Universe\Group;
use Seatplus\Eveapi\Models\Universe\Type;

use function Pest\Laravel\get;

test('esi search validates the search term length', function () {
    test()->actingAs(test()->test_user);

    get(route('autosuggestion.search', ['search' => 'J', 'categories' => ['system']]))
        ->assertInvalid(['search' => 'The search field must be at least 3 characters.']);
});

it('has auttosuggest for types, groups and categories', function () {
    $type = Type::factory()->create([
        'name' => 'TypeName',
        'group_id' => Group::factory()->create([
            'name' => 'GroupName',
            'category_id' => Category::factory()->create([
                'name' => 'CategoryName',
            ]),
        ]),
    ]);

    $terms = ['Typ', 'Grou', 'Cate'];

    foreach ($terms as $term) {
        $result = test()->actingAs(test()->test_user)
            ->get(route('autosuggestion.typesOrGroupOrCategories', ['search' => $term]))
            ->assertOk();

        expect($result->original)->toHaveCount(1);
    }
});
