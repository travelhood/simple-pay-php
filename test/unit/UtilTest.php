<?php

class UtilTest extends BaseTest
{
    public function testMergeArray()
    {
        $a1 = [
            'foo' => 1,
            'bar' => [
                'bax' => 1,
                'bop' => [
                    'plop' => 1,
                ],
            ],
            'a1' => 1,
        ];
        $a2 = [
            'foo' => 2,
            'bar' => [
                'bax' => 2,
                'bop' => [
                    'plop' => 2,
                ],
            ],
            'a2' => 2,
        ];
        $m = \Travelhood\OtpSimplePay\Util::mergeArray($a1, $a2);
        $this->assertEquals([
            'foo' => 2,
            'bar' => [
                'bax' => 2,
                'bop' => [
                    'plop' => 2,
                ],
            ],
            'a1' => 1,
            'a2' => 2,
        ], $m);
    }

    public function provideInterpolateString()
    {
        return [
            ['{%{foo}} bar', ['foo'=>'foo', 'bar'=>'bar'], '{foo} bar'],
            ['<input type="%{type}" name="%{name}" value="%{value}" />', ['type'=>'hidden', 'name'=>'inp', 'value'=>1], '<input type="hidden" name="inp" value="1" />'],
        ];
    }

    /**
     * @dataProvider provideInterpolateString
     * @param $template
     * @param $array
     * @param $expected
     */
    public function testInterpolateString($template, $array, $expected)
    {
        $this->assertEquals($expected, \Travelhood\OtpSimplePay\Util::interpolateString($template, $array));
    }
}