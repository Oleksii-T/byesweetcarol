<?php

namespace Tests\Feature;

use App\Http\Requests\Api\PostCreateRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PostCreateRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');

        DB::purge('sqlite');

        Schema::create('post_infos', function ($table) {
            $table->id();
            $table->string('source')->nullable();
        });
    }

    public function test_it_extracts_source_and_rejects_duplicates(): void
    {
        $request = $this->makeRequest('https://example.com/article');
        $request->prepareForValidationForTest();

        $validated = Validator::make($request->all(), $request->rules())->validate();

        $this->assertSame(
            'https://example.com/article',
            $validated['source'],
        );

        DB::table('post_infos')->insert([
            'source' => 'https://example.com/article',
        ]);

        $request = $this->makeRequest('https://example.com/article');
        $request->prepareForValidationForTest();
        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('source', $validator->errors()->toArray());
    }

    private function makeRequest(string $source): TestablePostCreateRequest
    {
        $request = TestablePostCreateRequest::create('/api/posts', 'POST', [
            'title' => 'Test post',
            'meta_title' => 'Test post',
            'meta_description' => 'Test post description',
            'body' => 'Test body',
            'external_data' => json_encode(['source' => $source]),
        ]);

        return $request->setContainer($this->app);
    }
}

class TestablePostCreateRequest extends PostCreateRequest
{
    public function prepareForValidationForTest(): void
    {
        parent::prepareForValidation();
    }
}
