<?php

namespace App\RealWorld\Follow;

use Auth;
use App\Models\User;

trait Followable
{
    /**
     * Check if the authenticated user is following this user.
     *
     * @return bool
     */
    public function getFollowingAttribute()
    {
        if (! Auth::check()) {
            return false;
        }
        return in_array(Auth::user()->id, $this->follower_ids, true);
    }

    /**
     * Follow the given user.
     *
     * @param User $user
     * @return mixed
     */
    public function follow(User $user)
    {
        if (! $this->isFollowing($user) && $this->id != $user->id) {
            return $this->following()->attach($user);
        }
    }

    /**
     * Unfollow the given user.
     *
     * @param User $user
     * @return mixed
     */
    public function unFollow(User $user)
    {
        return $this->following()->detach($user);
    }

    /**
     * Get all the users that this user is following.
     *
     * @return \Jenssegers\Mongodb\Relations\BelongsToMany
     */
    public function following()
    {
        return $this->belongsToMany(User::class, null, 'follower_ids', 'following_ids');
    }

    /**
     * Get all the users that are following this user.
     *
     * @return \Jenssegers\Mongodb\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, null, 'following_ids', 'follower_ids');
    }

    /**
     * Check if a given user is following this user.
     *
     * @param User $user
     * @return bool
     */
    public function isFollowing(User $user)
    {
        return !! $this->following()->where('following_ids', $user->id)->count();
    }

    /**
     * Check if a given user is being followed by this user.
     *
     * @param User $user
     * @return bool
     */
    public function isFollowedBy(User $user)
    {
        return !! $this->followers()->where('follower_ids', $user->id)->count();
    }
}