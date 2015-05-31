# Introduction #

There are certainly ways in which Plans can benefit from AJAX functionality, but let's take a careful approach. Judicious deployment of AJAX can make Plans a happier experience for the majority of users, without alienating or confusing anyone.


# Goals #

## Degrade Gracefully ##
Not everyone has Javascript working or turned on.  Some users may keep it off for security reasons. Others may access Plans from shared or public computers that are not up-to-date.  Still others may access Plans through browsers like lynx or screen readers that do not support Javascript.

If the appropriate Javascript bindings are not present, things should degrade gracefully - no nasty errors, no dead links, _no loss of functionality_.

## Preserve Navigation ##
Don't break the Back button. Provide bookmarkable URLs. Ideally, have links point to the appropriate URL, even if clicking them triggers AJAX rather than a page load.

## Provide Sufficient Feedback ##
Make sure users can tell that clicks are having an effect. This means something to indicate loading, plus a clear connection between each click and its effect on the page.

## Keep Eye Candy Reasonable ##
Shiny things are fine, _as long as they augment and do not distract_.  So maybe an element fading out is good, but an element spinning across the screen and exploding into 3D pieces isn't so great.

# Strategies #

A framework such as [Prototype](http://www.prototypejs.org/) may be useful for goals such as graceful degradation (plus it provides a lot of nice compatibility stuff under the hood).

We should be well served in most cases to add a Javascript layer on top of existing functionality, rather than rewriting everything.  After all, we need everything to continue functioning in the absence of JS.