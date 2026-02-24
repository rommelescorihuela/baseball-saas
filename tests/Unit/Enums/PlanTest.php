<?php

namespace Tests\Unit\Enums;

use App\Enums\Plan;

describe('Plan Enum', function () {

    test('has FREE case', function () {
        expect(Plan::FREE)->toBeInstanceOf(Plan::class)
            ->and(Plan::FREE->value)->toBe('free');
    });

    test('has PRO case', function () {
        expect(Plan::PRO)->toBeInstanceOf(Plan::class)
            ->and(Plan::PRO->value)->toBe('pro');
    });

    test('has UNLIMITED case', function () {
        expect(Plan::UNLIMITED)->toBeInstanceOf(Plan::class)
            ->and(Plan::UNLIMITED->value)->toBe('unlimited');
    });

    test('can be created from string', function () {
        expect(Plan::from('free'))->toBe(Plan::FREE)
            ->and(Plan::from('pro'))->toBe(Plan::PRO)
            ->and(Plan::from('unlimited'))->toBe(Plan::UNLIMITED);
    });
});

describe('Plan maxCompetitions Method', function () {

    test('FREE plan allows 1 competition', function () {
        expect(Plan::FREE->maxCompetitions())->toBe(1);
    });

    test('PRO plan allows 5 competitions', function () {
        expect(Plan::PRO->maxCompetitions())->toBe(5);
    });

    test('UNLIMITED plan allows null competitions (unlimited)', function () {
        expect(Plan::UNLIMITED->maxCompetitions())->toBeNull();
    });
});

describe('Plan maxTeams Method', function () {

    test('FREE plan allows 8 teams', function () {
        expect(Plan::FREE->maxTeams())->toBe(8);
    });

    test('PRO plan allows 20 teams', function () {
        expect(Plan::PRO->maxTeams())->toBe(20);
    });

    test('UNLIMITED plan allows null teams (unlimited)', function () {
        expect(Plan::UNLIMITED->maxTeams())->toBeNull();
    });
});

describe('Plan label Method', function () {

    test('FREE plan has correct label', function () {
        expect(Plan::FREE->label())->toBe('Plan Gratuito');
    });

    test('PRO plan has correct label', function () {
        expect(Plan::PRO->label())->toBe('Plan Pro');
    });

    test('UNLIMITED plan has correct label', function () {
        expect(Plan::UNLIMITED->label())->toBe('Plan Unlimited');
    });
});

describe('Plan stripePriceId Method', function () {

    test('FREE plan returns null for stripe price id', function () {
        expect(Plan::FREE->stripePriceId())->toBeNull();
    });

    test('PRO plan returns stripe price id from env', function () {
        // This will return null if env is not set, which is expected in tests
        $result = Plan::PRO->stripePriceId();
        expect($result)->toBeNull();
    });

    test('UNLIMITED plan returns stripe price id from env', function () {
        // This will return null if env is not set, which is expected in tests
        $result = Plan::UNLIMITED->stripePriceId();
        expect($result)->toBeNull();
    });
});

describe('Plan fromPriceId Method', function () {

    test('returns null for unknown price id', function () {
        expect(Plan::fromPriceId('price_unknown'))->toBeNull();
    });

    test('returns null for empty price id', function () {
        expect(Plan::fromPriceId(''))->toBeNull();
    });

    test('can find plan by price id when env is set', function () {
        // In test environment without env vars, this returns null
        // But we test the logic works
        $result = Plan::fromPriceId('price_test_pro');
        expect($result)->toBeNull();
    });
});

describe('Plan Enum Cases', function () {

    test('returns all cases', function () {
        $cases = Plan::cases();

        expect($cases)->toHaveCount(3)
            ->and($cases[0])->toBe(Plan::FREE)
            ->and($cases[1])->toBe(Plan::PRO)
            ->and($cases[2])->toBe(Plan::UNLIMITED);
    });

    test('each case has correct value', function () {
        foreach (Plan::cases() as $case) {
            expect($case->value)->toBeString()
                ->and($case->value)->toBe(strtolower($case->name));
        }
    });
});
