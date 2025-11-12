<?php

use App\Domain\Catalog\Models\Template;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->boolean('is_popular')->default(false)->after('is_active')->index();
            $table->boolean('is_new')->default(false)->after('is_popular')->index();
        });

        Template::query()->select(['id', 'metadata'])->chunkById(100, function ($templates) {
            /** @var \App\Domain\Catalog\Models\Template $template */
            foreach ($templates as $template) {
                $metadata = $template->metadata ?? [];
                $flags = $metadata['flags'] ?? [];

                $template->forceFill([
                    'is_popular' => (bool) ($template->is_popular ?? $flags['popular'] ?? $metadata['is_popular'] ?? false),
                    'is_new' => (bool) ($template->is_new ?? $flags['is_new'] ?? $metadata['is_new'] ?? false),
                ])->save();
            }
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['is_popular', 'is_new']);
        });
    }
};
