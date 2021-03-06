<?php

/**
 * This file is part of the TNetstring project and is copyright
 * 
 * (c) 2011 Sam Smith <me@phuedx.com>.
 *
 * Please refer the to LICENSE file that was distributed with this source code
 * for the full copyright and license information.
 */

class TNetstring_DecoderTest extends PHPUnit_Framework_TestCase
{
    protected $decoder;
    
    public function setUp() {
        $this->decoder = new TNetstring_Decoder();
    }
    
    public static function decodeDataProvider() {
        // The PHP equivalent of the test cases that can be found at
        // http://codepad.org/Uj42SuMo
        return array(
            array('0:}', array()),
            array('0:]', array()),
            array(
                "44:5:hello,32:5:12345#4:this,4:true!0:~4:\x00\x00\x00\x00,]}",
                array('hello' => array(12345, 'this', true, null, "\x00\x00\x00\x00"))
            ),
            array('5:12345#', 12345),
            array('12:this is cool,', 'this is cool'),
            array('0:,', ''),
            array('0:~', null),
            array('4:true!', true),
            array('5:false!', false),
            array("10:\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00,", "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"),
            array('24:5:12345#5:67890#5:xxxxx,]', array(12345, 67890, 'xxxxx')),
        );
    }
    
    /**
     * @dataProvider decodeDataProvider
     */
    public function testDecode($tnetstring, $expected) {
        $this->assertEquals($expected, $this->decoder->decode($tnetstring));
    }

    /**
     * @expectedException RuntimeException
     */
    public function testDecodeDetectsIntegerOverflow() {
        $value   = '9223372036854775808';
        $payload = sprintf('%d:%s#', strlen($value), $value);

        $this->decoder->decode($payload);
    }
}
