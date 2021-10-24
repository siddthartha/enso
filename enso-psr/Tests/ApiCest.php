<?php
namespace Enso\Tests;

class ApiCest
{    
    public function tryApi(ApiTester $I)
    {
        $I->sendGet('/');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'before' => 'integer:>0',
            'after' => 'integer:>0',
            'taskDuration' => 'float',
        ]);
    }
}