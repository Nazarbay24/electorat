<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin Builder
 */
class ProfileManager extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'profile_manager';
    public $timestamps = false;

    public function getGeo() {
        $geoCountry = new GeoCountry();
        $geoRegion = new GeoRegion();
        $geoPunkt = new GeoPunkt();
        $geoLocal = new GeoLocal();

        return $this->select(
                'country.text_ru as country',
                'region.text_ru as region',
                'punkt.text_ru as punkt',
                'local.text_ru as local',
            )
            ->where($this->getTable().'.id', $this->id)
            ->leftJoin($geoLocal->getTable().' as local', $this->getTable().'.local_id', '=', 'local.id')
            ->leftJoin($geoPunkt->getTable().' as punkt', 'local.punkt_id', '=', 'punkt.id')
            ->leftJoin($geoRegion->getTable().' as region', 'punkt.region_id', '=', 'region.id')
            ->leftJoin($geoCountry->getTable().' as country', 'region.country_id', '=', 'country.id')
            ->first();
    }

    public function getStatistics() {
        return [
            'all' => PollsRespondent::where('manager_id', $this->id)->count(),
            'today' => PollsRespondent::where('regdate', '>=', Carbon::today())->count(),
            'yesterday' => PollsRespondent::where('regdate', '>=', Carbon::yesterday())->where('regdate', '<', Carbon::today())->count(),
        ];
    }
}
