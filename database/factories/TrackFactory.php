<?php
use App\Models\Track;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackFactory extends Factory
{
    protected $model = Track::class;

    public function definition()
    {
        return [
            'filename' => $this->faker->unique()->slug().'.mp3',
            'relative_path' => $this->faker->randomElement(['shows/morning/','music/chill/','segments/tech/']),
            'duration_seconds' => $this->faker->numberBetween(60, 600),
            'performer' => $this->faker->firstName(),
            'category'  => $this->faker->randomElement(['News','Music','Interview']),
            'theme'     => $this->faker->randomElement(['Morning','Chill','Tech']),
            'year'      => $this->faker->numberBetween(2019, 2025),
        ];
    }
}