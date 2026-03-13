<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $fast_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $participant_user_id
 * @property string $fast_type
 * @property int|null $prayer_minutes
 * @property-read \App\Models\Person|null $leader
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Person> $leaders
 * @property-read int|null $leaders_count
 * @property-read \App\Models\User $participant
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fast newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fast newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fast query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fast whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fast whereFastDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fast whereFastType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fast whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fast whereParticipantUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fast wherePrayerMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fast whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFast {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $day_of_week
 * @property string $reminder_time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FastingSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FastingSchedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FastingSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FastingSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FastingSchedule whereDayOfWeek($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FastingSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FastingSchedule whereReminderTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FastingSchedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FastingSchedule whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFastingSchedule {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Fast> $fasts
 * @property-read int|null $fasts_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Person whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPerson {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $prayed_by
 * @property string $prayed_for
 * @property string $start_time
 * @property string $end_time
 * @property int|null $duration_minutes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prayer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prayer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prayer whereDurationMinutes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prayer whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prayer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prayer wherePrayedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prayer wherePrayedFor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prayer whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prayer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPrayer {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $leader_id
 * @property string $mode
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $started_at
 * @property \Illuminate\Support\Carbon|null $ended_at
 * @property int|null $duration_seconds
 * @property int|null $proclamation_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $prayer_date
 * @property int $active_seconds
 * @property \Illuminate\Support\Carbon|null $paused_at
 * @property bool $is_running
 * @property-read \App\Models\Person $leader
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereActiveSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereDurationSeconds($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereIsRunning($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereLeaderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession wherePausedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession wherePrayerDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereProclamationCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrayerSession whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperPrayerSession {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $shared_with_id
 * @property bool $can_view_stats
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereCanViewStats($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereSharedWithId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Relationship whereUserId($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperRelationship {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

