# Winter MediaStream

### Intro

This plugin adds streaming support for html5 videos.

### Installation

```shell
composer install jaxwilko/wn-mediastream-plugin
```

### Usage

Once installed, you can use the `video` component to add a video to your page:

```html
title = "Example"
url = "/example"
is_hidden = 0

[video]

==
{% styles %}
<div>
    {% component 'video' file='videos/example.mp4' %}
</div>
```

> If you're not using the `{% styles %}` tag, you'll need to include
> `/plugins/jaxwilko/mediastream/assets/css/video.css` yourself.

The video component supports the following properties:

| Name           | Type     | Description              |
|----------------|----------|--------------------------|
| `file`         | `string` | the file to display      |
| `disable_meta` | `bool`   | disable schema data      |
| `lazy`         | `bool`   | enable lazy load support |

> Lazy loading support is compatible with [verlok/vanilla-lazyload](https://github.com/verlok/vanilla-lazyload).

The `file` property is the path to your video within the media storage.

E.g. `file='videos/example.mp4'` will load `storage/app/media/videos/example.mp4` when using local storage.

#### Metadata

Video metadata can be added via the backend, this is used to support the `VideoObject` schema.

#### Thumbnails

By default, if the file is in local storage a thumbnail will be generated using ffmpeg. If the file is remote, then
ffmpeg will not be able to generate the thumbnail and you will have to supply your own. This can also be done via
the MediaStream Meta tab in the backend.

The meta thumbnail will also be used instead of the generated one if present.
