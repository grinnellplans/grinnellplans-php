<script>
<!--
function replace ()
{
    var keys = new Array(
	// For apostrophes, use \\'
	// Also, please make sure that your newly added words don't process any of the words in the alert text of earlier passes.  You can work around this by adusting the order.  Or use a &nbsp; ~[wellons]
     //{word: "", text: "Missed Reunion?? Graduating soon? Catch up on facebook!" },
    {word: "plans", text: "Upgrade to Plans Platinum!" },
    {word: "grinnellplans", text:  "Three term papers, two problem sets, and an SGA&nbsp; meeting to prepare for? You need www.grinnellplans.com"},
    {word: "osgood", text:  "Get rid of Ads for only $9.99 per month!" },
    {word: "iowa", text: "Cheap flights to Des Moines starting at $387.54"},
    {word: "Facebook", text: "When your autoread list is empty. www.thefacebook.com."},
    {word: "Beer", text: "http://www.naturallight.com"},
    {word: "Beers", text: "http://www.naturallight.com"},
    {word: "wine", text: "http://www.franzia.com/"},
    {word: "Secret", text: "New SecretsPLUS!  Find out who wrote a secret for only $5.98 + shipping and handling!"},
    {word: "Secrets", text: "New SecretsPLUS!  Find out who wrote a secret for only $5.98 + shipping and handling!"},
    {word: "Laiu", text: "http://www.grinnellplans.com/read.php?searchname=blah"},
    {word: "Grinnell", text: "No Limits. No! Limits. Know Limits. http://www.grinnell.edu/nolimits/"},
    {word: "graduation", text: "AHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHH! Must. Find. Anything. http://en.wikipedia.org/wiki/Super_senior"},
    {word: "graduating", text: "AHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHH! Must. Find. Anything. http://en.wikipedia.org/wiki/Super_senior"},
    {word: "graduate", text: "AHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHHH! Must. Find. Anything. http://en.wikipedia.org/wiki/Super_senior"},
    {word: "Spring Break", text: "Spring Break will be cut to a 12-hour resting period beginning next year due to the financial crisis.  Blaim AIG, not RKO.  Presented by Grinnell College and ACE."},
    {word: "Summer", text: "URGENT UPDATE: Expect severe sunnyness, extremely moderate high temperatures, and THUNDER SNOW (melted form only) in 4-8 weeks.  Sponsored by Stephen Briscoe, http://stephenbriscoesavedmylife.blogspot.com/."},
    {word: "Spring", text: "URGENT UPDATE: Nature is a tease.  Sponsored by Stephen Briscoe, http://stephenbriscoesavedmylife.blogspot.com/."},
    {word: "SGA", text: "Spending your student activity fee since 1846! http://grinnellsga.com/"},
    {word: "Barack", text: "Barack was a pawn.  POWER IS MINE! http://www.whitehouse.gov/administration/michelle_obama/."},
    {word: "Obama", text: "Barack was a pawn.  POWER IS MINE! http://www.whitehouse.gov/administration/michelle_obama/."},
    {word: "President", text: "Barack was a pawn.  POWER IS MINE! http://www.whitehouse.gov/administration/michelle_obama/."},
    {word: "Michelle", text: "Barack was a pawn.  POWER IS MINE! http://www.whitehouse.gov/administration/michelle_obama/."},
    {word: "google", text: "Google: Double-Plus Good. http://www.google.com/."},
    {word: "April", text: "Fools!"},
    {word: "like", text: "Confess your secret love on secrets!!"},
    {word: "go", text: "Go places.  Transfer to Carleton!!"},
    {word: "food", text: "Eat at Cowles!"},
    {word: "hippie", text: "Dial Soap 2-for-1 at HyVee, today only."},
    {word: "organic", text: "Dial Soap 2-for-1 at HyVee, today only."},
    {word: "mathlan", text: "Don\\'t take chances with the outside world. Buy Coppertone SPF 75."}

	
	// For apostrophes, use \\'
    
);
for (i=0;i<keys.length;i++) {
        var reg = new RegExp('( )' + keys[i]["word"] + '( )', "ig");
        var target = document.getElementById('change');
        target.innerHTML = target.innerHTML.replace(reg, '$1' + '<a class="inline" onmouseover="alert(\'' + keys[i]["text"] + '\')">' +  keys[i]["word"] + '</a>'+ '$2');

    }
}
-->
</script>  
