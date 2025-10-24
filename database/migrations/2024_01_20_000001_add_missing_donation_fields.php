use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'verification_status')) {
                $table->string('verification_status')->default('pending');
            }
            if (!Schema::hasColumn('donations', 'blockchain_status')) {
                $table->string('blockchain_status')->default('pending');
            }
            if (!Schema::hasColumn('donations', 'blockchain_tx_hash')) {
                $table->string('blockchain_tx_hash')->nullable();
            }
            if (!Schema::hasColumn('donations', 'is_anonymous')) {
                $table->boolean('is_anonymous')->default(false);
            }
            if (!Schema::hasColumn('donations', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            if (!Schema::hasColumn('donations', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->constrained('users');
            }
        });
    }

    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn([
                'verification_status',
                'blockchain_status',
                'blockchain_tx_hash',
                'is_anonymous',
                'verified_at',
                'verified_by'
            ]);
        });
    }
};