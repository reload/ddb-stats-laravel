<?php

use DDB\Stats\StatisticsController;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
class StatisticsControllerTest extends TestCase
{
    public function testGet()
    {
        $request = $this->prophesize(Request::class);
        $controller = new StatisticsController();
        $this->assertEquals(['here' => 'will be statistics'], $controller->get($request->reveal()));
    }
}
