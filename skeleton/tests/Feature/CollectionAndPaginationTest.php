<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Post;
use Switch\Foundation\Collection\Collection;
use Tests\TestCase;

class CollectionAndPaginationTest extends TestCase
{
    public function testCollectionTransformationsAndFilters(): void
    {
        $items = [
            ['id' => 1, 'category' => 'tech', 'price' => 100],
            ['id' => 2, 'category' => 'books', 'price' => 25],
            ['id' => 3, 'category' => 'tech', 'price' => 150],
            ['id' => 4, 'category' => 'tech', 'price' => 50],
        ];

        $collection = Collection::make($items);

        // Filter & Pluck
        $techPrices = $collection
            ->where('category', 'tech')
            ->pluck('price')
            ->values()
            ->all();

        $this->assertEquals([100, 150, 50], $techPrices);

        // Math & Aggregation
        $this->assertEquals(325, $collection->sum('price'));
        $this->assertEquals(81.25, $collection->avg('price'));
        $this->assertEquals(150, $collection->max('price'));
        $this->assertEquals(25, $collection->min('price'));

        // GroupBy
        $grouped = $collection->groupBy('category')->all();
        $this->assertCount(2, $grouped);
        $this->assertCount(3, $grouped['tech']);
        $this->assertCount(1, $grouped['books']);

        // Pipe & Tap
        $tapped = false;
        $piped = $collection->tap(function () use (&$tapped) {
            $tapped = true;
        })->pipe(function ($c) {
            return $c->count() * 10;
        });

        $this->assertTrue($tapped);
        $this->assertEquals(40, $piped);
    }

    public function testDatabasePaginationAndPaginatorArray(): void
    {
        for ($i = 1; $i <= 25; $i++) {
            Post::create([
                'title' => "Post {$i}",
                'slug' => "post-{$i}",
                'content' => "Content for post {$i}",
                'status' => 'published',
            ]);
        }

        $paginator = Post::query()->orderBy('id', 'asc')->paginate(perPage: 10, page: 2);

        $this->assertEquals(25, $paginator->total());
        $this->assertEquals(2, $paginator->currentPage());
        $this->assertEquals(10, $paginator->perPage());
        $this->assertEquals(3, $paginator->lastPage());
        $this->assertTrue($paginator->hasPages());
        $this->assertTrue($paginator->hasMorePages());
        $this->assertCount(10, $paginator->items());

        $firstItemOnPage2 = $paginator->items()[0];
        $this->assertEquals('Post 11', $firstItemOnPage2->title);

        $array = $paginator->toArray();
        $this->assertArrayHasKey('data', $array);
        $this->assertArrayHasKey('total', $array);
        $this->assertArrayHasKey('current_page', $array);
        $this->assertArrayHasKey('last_page', $array);
    }
}
