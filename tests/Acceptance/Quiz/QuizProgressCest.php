<?php

namespace App\Tests\Acceptance\Quiz;

use App\Tests\Support\AcceptanceTester;

class QuizProgressCest
{
    public function _before(AcceptanceTester $I): void
    {
    }

    public function withNewSession(AcceptanceTester $I): void
    {
        // Default page
        $I->amOnPage('/');
        $I->see('New session');
        $I->canSee('Continue session (0 of 10)');

        // First question and submit
        $I->click('New session');
        $I->seeInTitle('Question page');
        $I->checkOption('body > div > form > div > div:nth-child(1) > label > input');
        $I->click('Submit');

        // Return to start page, check progress
        $I->click('Start page');
        $I->see('Continue session (1 of 10)');

        // New session and return to start page (reset progress)
        $I->click('New session');
        $I->click('Start page');
        $I->see('Continue session (0 of 10)');
    }
}
