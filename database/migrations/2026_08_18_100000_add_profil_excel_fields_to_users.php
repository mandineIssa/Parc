<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('filiales')) {
            Schema::create('filiales', function (Blueprint $table) {
                $table->id();
                $table->string('nom')->unique();
                $table->boolean('actif')->default(true);
                $table->timestamps();
            });
        }

        DB::table('filiales')->updateOrInsert(
            ['nom' => 'Sénégal'],
            ['actif' => true, 'updated_at' => now(), 'created_at' => now()]
        );

        if (Schema::hasTable('agencies') && ! Schema::hasColumn('agencies', 'filiale_id')) {
            Schema::table('agencies', function (Blueprint $table) {
                $table->foreignId('filiale_id')->nullable()->after('id')->constrained('filiales')->nullOnDelete();
            });
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'matricule')) {
                $table->string('matricule', 50)->nullable()->unique()->after('id');
            }
            if (! Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone', 40)->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'type_contrat')) {
                $table->string('type_contrat', 20)->default('CDI')->after('fonction');
            }
            if (! Schema::hasColumn('users', 'statut')) {
                $table->string('statut', 20)->default('actif')->after('type_contrat');
            }
            if (! Schema::hasColumn('users', 'agency_id')) {
                $table->foreignId('agency_id')->nullable()->after('departement')->constrained('agencies')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'filiale_id')) {
                $table->foreignId('filiale_id')->nullable()->after('agency_id')->constrained('filiales')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'n_plus_1_id')) {
                $table->foreignId('n_plus_1_id')->nullable()->after('filiale_id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'n_plus_2_id')) {
                $table->foreignId('n_plus_2_id')->nullable()->after('n_plus_1_id')->constrained('users')->nullOnDelete();
            }
        });

        $senegalId = DB::table('filiales')->where('nom', 'Sénégal')->value('id');
        if ($senegalId) {
            User::query()->whereNull('filiale_id')->update(['filiale_id' => $senegalId]);
        }

        $index = 1;
        User::query()->whereNull('matricule')->orWhere('matricule', '')->orderBy('id')->each(function (User $user) use (&$index) {
            $user->matricule = str_pad((string) $index, 6, '0', STR_PAD_LEFT);
            $user->saveQuietly();
            $index++;
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['n_plus_2_id', 'n_plus_1_id', 'filiale_id', 'agency_id'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropConstrainedForeignId($column);
                }
            }
            foreach (['matricule', 'telephone', 'type_contrat', 'statut'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        if (Schema::hasColumn('agencies', 'filiale_id')) {
            Schema::table('agencies', function (Blueprint $table) {
                $table->dropConstrainedForeignId('filiale_id');
            });
        }

        Schema::dropIfExists('filiales');
    }
};
