<?php
namespace MarcoConsiglio\Trigonometry\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Faker\Factory;

class TestCase extends PHPUnitTestCase
{
    /**
     * The faker generator.
     *
     * @var \Faker\Generator;
     */
    protected $faker;

   /*
    * This method is called before each test.
    */
   protected function setUp(): void
   {
       parent::setUp();
       $this->faker = Factory::create();
   }
}