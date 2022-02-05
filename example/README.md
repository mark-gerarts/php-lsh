# Example usage

The `nlwiki.php` script analyses a Wikipedia dataset to find similar titles.

## Usage

First, make sure the dataset is downloaded and uncompressed:

```console
wget "https://dumps.wikimedia.org/nlwiki/latest/nlwiki-latest-all-titles-in-ns0.gz" \
  && gunzip nlwiki-latest-all-titles-in-ns0.gz
```

Install the main library if needed:

```
cd .. && composer install && cd example
```

Then simply run the PHP script:

```console
php -d memory_limit=-1 nlwiki.php
```

By default this will only look at the first 50k titles (takes ~15s). Adjust the 
limit by passing it as a parameter (-1 for unlimited).

```console
php -d memory_limit=-1 nlwiki.php 100000
```

The result looks like this (t=0.9, b=5, r=20, n=100, character ngram 5):

```console
php -d memory_limit=-1 nlwiki.php 100000 | head -n 20
"!_(doorverwijspagina)" and "1000_(doorverwijspagina)"
""A"_You're_Adorable" and "'A'_You're_Adorable"
"'Abd_al-Rahmān_III" and "Abd_al-Rahmān_III"
"'k_Heb_je_lief_-_50_Jaar_de_muziek,_m'n_fans,_het_leven" and "'k_Heb_je_lief_-_50_jaar_de_muziek,_m'n_fans,_het_leven"
"'s-Gravelandsepolder" and "'s-Gravelandsepolder_('s-Graveland)"
"'s-Gravenpolder" and "'s-Gravenpolder_(dorp)"
"'t_Is_weer_voorbij_die_mooie_zomer" and "'t_Is_weer_voorbij_die_mooie_zomer_(album)"
"(253)_Mathilda" and "(253)_Mathilde"
"(4440)_Tchantches" and "(4440)_Tchantchès"
"(I'm_so)_Afraid_of_losing_you_again" and "(I'm_so)_afraid_of_losing_you_again"
"(If_Paradise_Is)_Half_As_Nice" and "(If_Paradise_Is)_Half_As_Nice_(lied)"
"(Sittin'_On)_The_Dock_of_the_Bay" and "(Sittin'_on)_The_Dock_of_the_Bay"
"(What's_the_Story)_Morning_Glory" and "(What's_the_Story)_Morning_Glory?"
"(What's_the_story)_Morning_glory" and "(What's_the_story)_Morning_glory?"
"(You_Gotta)_Fight_for_Your_Right_(To_Party!)" and "(You_Gotta)_Fight_for_Your_Right_(to_Party!)"
"+_(Ed_Sheeran)" and "=_(Ed_Sheeran)"
"...baby_one_more_time" and "...baby_one_more_time_(single)"
"...continuavano_a_chiamarlo_Trinita" and "...continuavano_a_chiamarlo_Trinità"
"0_(getal)" and "1000000_(getal)"
"0_(getal)" and "150_(getal)"
```
