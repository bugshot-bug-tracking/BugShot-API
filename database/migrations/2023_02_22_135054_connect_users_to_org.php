<?php

use App\Models\CompanyUserRole;
use App\Models\Organization;
use App\Models\OrganizationUserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Only execute once for the existing live data
        $orgs = Organization::all();
        $orgRolesArray = array();
        foreach ($orgs as $org) {
            foreach ($org->companies as $company) {
                foreach ($company->users as $user) {
                    //check if already exists
                    $userRole = OrganizationUserRole::all()
                        ->where('organization_id', '=', $org->id)
                        ->where('user_id', '=', $user->id);
                    if($userRole->first() != null){
                        continue;
                    }

                    $orgRole = OrganizationUserRole::create([
                        "organization_id" => $org->id,
                        "user_id" => $user->id,
                        "role_id" => 2,
                    ]);
                    $orgRolesArray[] = $orgRole;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //clear org role privot table?
    }
};
