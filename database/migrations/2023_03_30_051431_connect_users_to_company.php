<?php

use App\Models\CompanyUserRole;
use App\Models\Company;
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
        $companies = Company::all();

		foreach($companies as $company) {
			$companyUserRole = CompanyUserRole::where("company_id", $company->id)->where("user_id", $company->user_id)->first();

			if(!$companyUserRole) {
				if($company->creator) {
					$company->creator->companies()->attach($company->id, ['role_id' => 0]);
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
        //
    }
};
