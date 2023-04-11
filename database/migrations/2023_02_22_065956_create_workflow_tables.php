<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use EmzD\Workflow\Models\Event;
use EmzD\Workflow\Models\Place;
use EmzD\Workflow\Models\Transition;
use EmzD\Workflow\Models\Workflow;
use EmzD\Workflow\Traits\TablePrefix;

return new class extends Migration {
    use TablePrefix;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(Workflow::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('type', ['state_machine', 'workflow'])->default('workflow');
            $table->string('supports');
            $table->string('marking_property');
            $table->boolean('active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
        Schema::create(Place::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('workflow_id')->constrained(table: Workflow::getTableName())->cascadeOnDelete();
            $table->boolean('initial')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['name', 'workflow_id']);
        });
        Schema::create(Transition::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('workflow_id')->constrained(table: Workflow::getTableName())->cascadeOnDelete();
            $table->string('guard')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->unique(['name', 'workflow_id']);
        });
        Schema::create($this->getPrefix() . 'from_place_transition', function (Blueprint $table) {
            $table->foreignId('from_id')->constrained(table: Place::getTableName())->cascadeOnDelete();
            $table->foreignId('transition_id')->constrained(table: Transition::getTableName())->cascadeOnDelete();
        });
        Schema::create($this->getPrefix() . 'to_place_transition', function (Blueprint $table) {
            $table->foreignId('to_id')->constrained(table: Place::getTableName())->cascadeOnDelete();
            $table->foreignId('transition_id')->constrained(table: Transition::getTableName())->cascadeOnDelete();
        });
        $events = [
            ['name' => 'guard', 'scope' => 'transitions', 'configurable' => false],
            ['name' => 'transition', 'scope' => 'transitions'],
            ['name' => 'enter', 'scope' => 'places'],
            ['name' => 'leave', 'scope' => 'places'],
            ['name' => 'entered', 'scope' => 'places'],
            ['name' => 'completed', 'scope' => 'transitions'],
            ['name' => 'announce', 'scope' => 'transitions'],
        ];
        Schema::create(Event::getTableName(), function (Blueprint $table) use ($events) {
            $table->id();
            $table->enum('name', array_column($events, 'name'))->unique();
            $table->enum('scope', ['places', 'transitions']);
            $table->boolean('configurable')->default(true);
        });
        DB::table(Event::getTableName())->insert(array_map(function ($row) {
            if (!array_key_exists($k = 'configurable', $row)) {
                $row[$k] = true;
            }
            return $row;
        }, $events));
        Schema::create($this->getPrefix() . 'workflow_events', function (Blueprint $table) {
            $table->foreignId('workflow_id')->constrained(table: Workflow::getTableName())->cascadeOnDelete();
            $table->foreignId('event_id')->constrained(table: Event::getTableName())->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->getPrefix() . 'from_place_transition');
        Schema::dropIfExists($this->getPrefix() . 'to_place_transition');
        Schema::dropIfExists($this->getPrefix() . 'workflow_events');
        Schema::dropIfExists(Transition::getTableName());
        Schema::dropIfExists(Place::getTableName());
        Schema::dropIfExists(Event::getTableName());
        Schema::dropIfExists(Workflow::getTableName());
    }
};