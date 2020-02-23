<?php

namespace Miracuthbert\Royalty\Traits;

use Miracuthbert\Royalty\Actions\ActionAbstract;
use Miracuthbert\Royalty\Events\PointsGiven;
use Miracuthbert\Royalty\Exceptions\PointModelMissingException;
use Miracuthbert\Royalty\Formatters\PointsFormatter;
use Miracuthbert\Royalty\Models\Point;

trait CollectsPoints
{
    /**
     * The "booting" method of the trait.
     *
     * @return void
     *
     */
    public static function bootCollectsPoints()
    {
        //
    }

    /**
     * Get the sum of user's points.
     *
     * @return mixed
     */
    public function points()
    {
        return new PointsFormatter(
            $this->pointsRelation->sum('points')
        );
    }

    /**
     * Add given point to user.
     *
     * @param \Miracuthbert\Royalty\Actions\ActionAbstract $action
     * @return void
     * @throws PointModelMissingException
     */
    public function givePoints(ActionAbstract $action)
    {
        if (!$model = $action->getModel()) {
            throw new PointModelMissingException(
                __('Points model for key [:key] not found.', ['key' => $action->key()])
            );
        }

        $this->pointsRelation()->attach($model);

        event(new PointsGiven($this, $model));
    }

    /**
     * Get the user's points.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function pointsRelation()
    {
        return $this->belongsToMany(Point::class)
            ->withTimestamps();
    }
}
