# Implementing responsive media support out of the box in KeyMedia

We want to support responsive medias from KeyMedia. This should *just work* for existing sites,
while also being powerful enough for highend implementations without overriding everything.
Responsive Web Design (RWD) and Responsive Media (RM) are centered around a set of breakpoints.
that dictates what CSS rules should be applied once a BR is crossed.

Breakpoints are typically 480px, 768px, 960px, ~1200px.
By using Media Queries one can include a set of CSS rules once the screenwidth/height is above/below one of the breakpoints.

## Requirements

1. Best practice breakpoints should "just work" by default
2. Definining custom breakpoints must be possible
3. Semantic markup should be used
4. Page should not flicker
5. If everything crashes, an image should still be served
6. Versions should preferrerably get built from a focus point
7. ... but still allow specific crops like today
8. *Retina* images should just work

## Techniques

### Media Query changes in JS

It is possible to listen to Media Query changes in JavaScript and trigger updates based on the same rules as used in CSS.
One major caveat is that they need to be typed out in JavaScript, meaning you get duplicated mediaqueries so things might not be in sync.
Another issue is that mediaqueries respond to screen information, while in some cases it would be more interesting to listen to the container size.

### Switch img-src using JavaScript

The way we can update to using the correct media version is by replacing the `<img src="">` after initial load.

### Dictate HTML rendering with cookie

We could set a cookie with the screen size and share that with the server and have it modify its HTML
on the next page load.
This has a ton of problems in terms of caching, cdns etc.

### Default image

In order to avoid flickering we can serve a default img straight up and replace this later on.
This however gives high chances of loading two versions after size detection happens.

### Filler image

It is possible to default to using an image with the same size we expect to use, this avoids flickering.
However this breaks `#5`, if the placeholder is not replaced we will just see the placeholder.

## Issues

These are particular problematic areas in the current eZ Publish integration:

1. Different ContentClasses does not share crops, yet responsive breakpoints should work just by config, one config
2. Videos, bound to be a bitch
3. Media queries are not supported in IE6-8 ...

## Resources

* [media queries for common device breakpoints](http://alpha.responsivedesign.is/develop/browser-feature-support/media-queries-for-common-device-breakpoints)
* [enquire.js](http://wicky.nillia.ms/enquire.js/) Fast and lightweight JS lib to listen for media queries
* [js-breakpoints](https://github.com/14islands/js-breakpoints) enquire.js alternative
* [Respond.js](https://github.com/scottjehl/Respond) Polyfill for media queries by min/max-width in IE 6-8
* [NRK.no](http://nrkbeta.no/2013/04/08/responsive-nettsider-pa-nrk-no/) We can in part base our work on NRK.no
* [window.matchMedia basics](https://hacks.mozilla.org/2012/06/using-window-matchmedia-to-do-media-queries-in-javascript/) the JavaScript API making JS mediaqueries work
* going further ...
* [Behavioural breakpoints](http://blog.cloudfour.com/behavioral-breakpoints/)
* [Media queries are a hack](http://ianstormtaylor.com/media-queries-are-a-hack/) the case for element queries
