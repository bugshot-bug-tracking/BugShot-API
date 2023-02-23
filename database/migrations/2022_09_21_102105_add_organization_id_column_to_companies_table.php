<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyUserRole;
use Illuminate\Support\Str;
use App\Models\Organization;
use App\Services\GetUserLocaleService;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->after('user_id', function ($table) {
                $table->string('organization_id')->nullable();
                $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('set null');
            });
        });

        // Only execute once for the existing live data
        $users = User::all();
        foreach ($users as $user) {

            $id = (string) Str::uuid();
            $organization = Organization::create([
                "id" => $id,
                "user_id" => $user->id,
                "designation" => __('data.my-organization', [], GetUserLocaleService::getLocale($user)) . " (" . $user->first_name . " " . $user->last_name . ")"
            ]);

            $companies = $user->createdCompanies;
            if ($companies->isNotEmpty()) {
                foreach ($companies as $company) {
                    $company->update([
                        "organization_id" => $organization->id
                    ]);

                    $companyUserRole = CompanyUserRole::where("user_id", $company->creator->id)
                        ->where("company_id", $company->id)->first();
                    if ($companyUserRole) {
                        $company->users->detach($company->creator);
                    }
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
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign('companies_organization_id_foreign');
            $table->dropColumn('organization_id');
        });
    }
};
