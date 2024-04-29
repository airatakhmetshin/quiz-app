<?php

namespace Acceptance\Quiz;

use App\Tests\Support\AcceptanceTester;

class OpenPageCest
{
    public function _before(AcceptanceTester $I): void
    {
    }

    public function openStartPage(AcceptanceTester $I): void
    {
        $I->amOnPage('/');

        $I->seeInTitle('Start page');
        $I->canSeeResponseCodeIs(200);
    }

    public function openQuestionPage(AcceptanceTester $I): void
    {
        $I->amOnPage('/question');

        $I->seeInTitle('Question page');
        $I->canSeeResponseCodeIs(200);
    }

    public function openResultPage(AcceptanceTester $I): void
    {
        $I->amOnPage('/result');

        $I->canSeeResponseCodeIs(200);
    }
}
