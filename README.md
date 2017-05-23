#### Issue Tracker integrated to [Humhub](https://github.com/humhub/humhub)

The tracker focused on Enterprise and Private Social Networks.
![screenshot of one issue](https://cloud.githubusercontent.com/assets/874234/25657653/907516a0-3007-11e7-9430-0956bc2febf2.jpg)

## Features
- You can create tasks, issue, schedule meetings and other for your spaces.
- You can create private, personal tasks. Manage all available for you tasks using the dashboard.
- You can add personal colored tags(bookmarks) to issues .
- Cooperate on joint tasks. Share an unlimited number of tasks with anyone - colleagues, family members, friends and
 collaborate in real time on collaborative projects and goals.
- Manage large complex issues, breaking them into smaller subtasks.
- A large functions of Humhub is available for tasks. Issues by spaces, commenting, attaching files, Rich Markdown,
 notifications and others. Thanks HumHub Community Edition.
- Calendar and Timeline plugins are available for your analysis by tasks.

### To Install Manual
- upload all files to `protected/modules/tracker/`
- init module in admin setting and in your spaces

### To Update Manual
**Module is enabled, and activate:**

- delete all files from `protected/modules/tracker/`
- upload github files to `protected/modules/tracker/`
- run `cd protected/modules/tracker`
- run `php ../../yii migrate -p="migrations"`
- clear assets from `assets/` and cache from `protected/runtime/cache/`

### Improvements for translation.

If you want enhancement translation for your language just update files in [messages/your-languages](messages/)
follow this example code:

```php
return [
    'Issue' => 'Your translation for `Issue` message',
    'Tracker issues' => 'Your translation for `Tracker issues` message',
];
```

After send Pull Request to [Tracker module repository](https://github.com/githubjeka/tracker-issues)
