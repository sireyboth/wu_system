<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommuneResource;
use App\Http\Resources\DistrictResource;
use App\Http\Resources\NationalityResource;
use App\Http\Resources\ProvinceResource;
use App\Http\Resources\VillageResource;
use App\Models\Commune;
use App\Models\District;
use App\Models\Nationality;
use App\Models\Province;

class AddressController extends Controller
{
    public function provinces()
    {
        return ProvinceResource::collection(Province::all());
    }

    public function nationalities()
    {
        return NationalityResource::collection(Nationality::all());
    }

    public function districts(Province $province)
    {
        return DistrictResource::collection($province->districts);
    }

    public function communes(District $district)
    {
        return CommuneResource::collection($district->communes);
    }

    public function villages(Commune $commune)
    {
        return VillageResource::collection($commune->villages);
    }
}
