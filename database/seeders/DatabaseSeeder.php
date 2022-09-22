<?php

namespace Database\Seeders;

use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\AdDao;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\AdTagDao;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\FavoriteDao;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\NotificationDao;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\ReviewDao;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\SearchDao;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\TagDao;
use Beagle\Core\Infrastructure\Persistence\Eloquent\Models\UserDao;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Symfony\Component\Uid\Uuid;
use Tests\MotherObjects\PhonePrefixMotherObject;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user1Id = "N9pBnTwYhrN9tHJPrgDvsb";
        $user2Id = "Uswhgx53GAFmNvJHP6p8kn";
        $adId1 = "9uaG3n4k9AqLQBJJL268FR";
        $adId2 = "EfZWeDuuEPefWZqZkwF8iv";
        $tagId1 = "HNwU5W2e2rTeKHhWKGdXf4";
        $tagId2 = "Rp5QbDwfcc7bdk5zSAC4rk";

        if (empty(UserDao::count())) {
            $this->createUserDao($user1Id);
            $this->createUserDao($user2Id);
        }

        if (empty(NotificationDao::count())) {
            $this->createNotificationDao($user1Id);
            $this->createNotificationDao($user2Id);
        }

        if (empty(SearchDao::count())) {
            $this->createSearchDao($user1Id);
            $this->createSearchDao($user2Id);
        }

        if (empty(AdDao::count())) {
            $this->createAdDao(
                $user1Id,
                $adId1
            );
            $this->createAdDao(
                $user2Id,
                $adId2
            );
        }

        if (empty(ReviewDao::count())) {
            $this->createReviewDao(
                $user1Id,
                $user2Id,
                $adId1
            );
            $this->createReviewDao(
                $user2Id,
                $user1Id,
                $adId2
            );
        }

        if (empty(FavoriteDao::count())) {
            $this->createFavoriteDao(
                $user1Id,
                $adId2
            );
            $this->createFavoriteDao(
                $user2Id,
                $adId1
            );
        }

        if (empty(TagDao::count())) {
            $this->createTagDao($tagId1);
            $this->createTagDao($tagId2);
        }

        if (empty(AdTagDao::count())) {
            $this->createAdTagDao(
                $tagId1,
                $adId1
            );
            $this->createAdTagDao(
                $tagId2,
                $adId2
            );
        }
    }

    private function createUserDao(string $userId):void
    {
        UserDao::updateOrCreate(
            ["id" => $userId],
            [
                "email" => Factory::create()->email,
                "password" => md5("1234"),
                "name" => Factory::create()->name,
                "surname" => Factory::create()->lastName,
                "bio" => Factory::create()->text,
                "location" => Factory::create()->locale,
                "phone_prefix" => PhonePrefixMotherObject::create()->value(),
                "phone" => Factory::create()->phoneNumber,
                "picture" => Factory::create()->filePath(),
                "show_reviews" => Factory::create()->boolean,
                "rating" => Factory::create()->numberBetween(0, 5),
                "is_verified" => Factory::create()->boolean
            ]
        );
    }

    private function createNotificationDao(string $userId):void
    {
        NotificationDao::updateOrCreate(
            ["id" => Uuid::v4()->toBase58()],
            [
                "user_id" => $userId,
                "text" => Factory::create()->text,
                "read" => Factory::create()->boolean,
            ]
        );
    }

    private function createSearchDao(string $userId):void
    {
        SearchDao::updateOrCreate(
            ["id" => Uuid::v4()->toBase58()],
            [
                "user_id" => $userId,
                "text" => Factory::create()->text,
            ]
        );
    }

    private function createAdDao(
        string $userId,
        string $adId
    ):void {
        AdDao::updateOrCreate(
            ["id" => $adId],
            [
                "user_id" => $userId,
                "title" => Factory::create()->realText(50),
                "status" => Factory::create()->realText(10),
                "type" => Factory::create()->realText(10),
                "last_location" => Factory::create()->locale,
                "description" => Factory::create()->text,
                "reward" => Factory::create()->randomNumber(),
            ]
        );
    }

    private function createReviewDao(
        string $userId,
        string $authorId,
        string $adId
    ):void {
        ReviewDao::updateOrCreate(
            ["id" => Uuid::v4()->toBase58()],
            [
                "user_id" => $userId,
                "author_id" => $authorId,
                "ad_id" => $adId,
                "text" => Factory::create()->text,
                "rating" => Factory::create()->numberBetween(0, 5),
            ]
        );
    }

    private function createFavoriteDao(
        string $userId,
        string $adId
    ):void {
        FavoriteDao::updateOrCreate(
            ["id" => Uuid::v4()->toBase58()],
            [
                "user_id" => $userId,
                "ad_id" => $adId,
            ]
        );
    }

    private function createTagDao(string $tagId):void
    {
        TagDao::updateOrCreate(
            ["id" => $tagId],
            [
                "title" => Factory::create()->realText(10),
            ]
        );
    }

    private function createAdTagDao(
        string $tagId,
        string $adId,
    ):void {
        AdTagDao::updateOrCreate(
            ["id" => Uuid::v4()->toBase58()],
            [
                "tag_id" => $tagId,
                "ad_id" => $adId,
            ]
        );
    }
}
