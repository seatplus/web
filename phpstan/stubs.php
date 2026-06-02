<?php

// PHPStan stub file: adds @property annotations for Eloquent Attribute accessors
// that Larastan cannot infer from Attribute::make() without explicit return types.
// These stubs shadow the installed eveapi vendor models until eveapi releases a
// version with the proper @return Attribute<X,never> docblocks.

namespace Seatplus\Eveapi\Models;

/**
 * @property int $decision_count
 */
class Application {}

namespace Seatplus\Eveapi\Models\Recruitment;

/**
 * @property int $steps_count
 */
class Enlistments {}

namespace Seatplus\Eveapi\Models\Character;

class CharacterAffiliation {}

namespace Seatplus\Eveapi\Models\Contacts;

use Seatplus\Eveapi\Models\Character\CharacterAffiliation;

/**
 * @property ?CharacterAffiliation $affiliation
 */
class Contact {}
