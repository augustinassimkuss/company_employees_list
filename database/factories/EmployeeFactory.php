<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $max_id = Company::get();
        $count = $max_id->count();

        return [
            'full_name' => $this->faker->firstName() .' '. $this->faker->lastName(),
            'email' => $this->faker->unique()->email,
            'phone_number' => '3706' . $this->faker->randomNumber($nbDigits = 7, $strict=true),
            'company_id' => $this->faker->numberBetween($min = 1, $max = $count),
        ];
    }
}
