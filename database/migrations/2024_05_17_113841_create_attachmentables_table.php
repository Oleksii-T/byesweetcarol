<?php

use App\Models\Attachment;
use App\Models\Attachmentable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attachmentables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attachment_id')->constrained()->onDelete('cascade');
            $table->bigInteger('attachmentable_id')->nullable();
            $table->string('attachmentable_type')->nullable();
            $table->string('group')->nullable();
            $table->timestamps();
        });

        foreach (Attachment::all() as $a) {
            Attachmentable::create([
                'attachment_id' => $a->id,
                'attachmentable_id' => $a->attachmentable_id,
                'attachmentable_type' => $a->attachmentable_type,
                'group' => $a->group,
            ]);
        }

        Schema::table('attachments', function (Blueprint $table) {
            // $table->dropColumn('attachmentable_id');
            // $table->dropColumn('attachmentable_type');
            // $table->dropColumn('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('attachmentables');
    }
};
