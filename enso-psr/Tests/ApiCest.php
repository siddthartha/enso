<?php
class ApiCest
{    
    public function tryApiDefaultRoute(ApiTester $I)
    {
        $I->sendGet('/');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'before' => 'float:>0',
            'after' => 'float:>0',
            'taskDuration' => 'string',
        ]);
    }

    public function tryDefaultIndex(ApiTester $I)
    {
        $I->sendGet('/default/index');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'context' => [
                'sapi' => 'string',
                'swoole' => 'boolean',
            ],
        ]);
    }

    public function tryDefaultView(ApiTester $I)
    {
        $I->sendGet('/default/view');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'work' => 'done'
        ]);
    }

    public function tryBadRoute(ApiTester $I)
    {
        $I->sendGet('/some/bad/link');
        $I->seeResponseCodeIsServerError();
        $I->seeResponseCodeIs(500);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'class' => 'string',
            'file' => 'string',
            'line' => 'integer:>0',
            'message' => 'string',
        ]);
    }

}