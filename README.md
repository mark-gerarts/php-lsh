# Near-neighbour search using Locality-Sensitive Hashing (LSH), implemented in PHP

This implementation was created as a way to enhance my understanding of LSH and
mainly as something fun to do. I highly doubt this scales well.

1. [The theory](#locality-sensitive-hashing-the-theory)
2. [Usage](#usage)
3. [Example](#example)
4. [Tests](#tests)

## Locality-Sensitive Hashing: the theory

The implementation is based on the theory from
[Mining of Massive Datasets](http://www.mmds.org/), specifically
[Chapter 3](http://infolab.stanford.edu/~ullman/mmds/ch3n.pdf). We will only go
over the basic ideas here.

LSH consists of three steps:

1. Shingling
2. Min-hashing
3. LSH

### Shingling

*Shingling* converts documents to sets while still taking the ordering of words
into account. This works better for similarity checking than for example simply
taking the set of words that appear in the document.

To shingle a document we take a "sliding window" of *k-shingles* (k-gram, ngram)
of tokens. A token can be anything: characters, words, sentences, ...

For example, if we want to take word-shingles of length 2 of the following
sentence:

> The quick brown fox jumps over the lazy dog

The resulting set of shingles would be: "The quick", "quick brown", "brown fox",
"fox jumps", "jumps over", "over the", "the lazy", "lazy dog".

Or character-based shingles of length 3 of the string "example": "exa", "xam",
"amp", "mpl", "ple".

You can tweak this process in many ways, for example by ignoring stopwords or
punctuation. A more exotic alternative only takes into account the first *k*
words following a stopword.

### Min-hashing

Shingling a document results in a large set of shingles for long documents. To
preserve memory, we are interested in some way to compress the sets while still
maintaining similarity characteristics between pairs of documents. We can
achieve this by applying *min-hashing*.

To create a min-hash of a document, we do the following:

1. Create a number of independent hash functions *H1*, *H2*, ..., *Hn*. These
   are the same for every document.
2. Extract all shingles from the document
3. For each hash function *Hi* do:
   1. Hash every shingle
   2. Append the minimal hash to the min-hash vector

The result of this is a vector with a length equal to the number of hash
functions used. For further details on *why* this is a good way to compress sets
while maintaining similarity, we refer to section 3.3 of MMDS.

### LSH

We now have a list of min-hash vectors, one for each document. Even with the
smaller memory footprint, it is still time-consuming to compare every pair of
min-hashes for large sets of documents.

The basic idea of LSH is to apply a hash function to every min-hash and see
which vectors get hashed to the same bucket. These will be your candidate items:
items with a high probability of being similar. Normally you then do a second
pass where you actually compare the candidates to filter out any false
positives.

In order to minimize the false positive and false negative results, we do not
simply hash the entire min-hash vector, but instead apply the *banding
technique*. Each min-hash vector gets divided in *b* bands of *r* rows each. We
then hash every band separately. Two documents are considered candidate items if
the fraction of bands that hash to the same bucket is above some threshold *t*.

To achieve the best results, you should pick *b* and *r* in such a way that *t ~
(1/b)^(1/r)*. This process is explained in more detail in section 3.4 of MMDS.

## Usage

Basic usage:

```php
<?php

use PhpLsh\LSH;

// `$inputs` can be any iterable of strings.
$inputs = [
    'The quick brown fox jumps over the lazy dog.',
    'Sphinx of black quartz, judge my vow.',
    'The five boxing wizards jump quickly.',
    'My girl wove six dozen plaid jackets before she quit.',
    'Sixty zippers were quickly picked from the woven jute bag.',
    'A wizard’s job is to vex chumps quickly in fog.',
    'We promptly judged antique ivory buckles for the next prize.',
    'Pack my box with five dozen liquor jugs.',
    'The quick brown cat jumps over the lazy dog.'
];

// The defaults are explained later on.
$lsh = LSH::createWithDefaults();

// The return value is an array of tuples containing the indices of the
// candidate items.
$lsh->findCandidateItems($inputs);

foreach ($candidates as [$a, $b]) {
    echo sprintf('"%s" and "%s"', $inputs[$a], $inputs[$b]) . PHP_EOL;
}
```

Creating an instance of the LSH class can be configured as follows:

```php
// The LSH constructor.
public function __construct(
    // Determines how the input strings are tokenized (shingled). By default
    // we use a character based tokenizer.
    private TokenizerInterface $tokenizer,
    // Creates minhash signatures based on the tokenized input strings. By
    // default an instance is used that creates signatures of length 100 and
    // uses a basic hashing algorithm.
    private MinHash $minHash,
    // Determines the hashing function that hashes bands to buckets
    // (string -> int). By default a basic naive implementation is used.
    private HashInterface $hash
) {}

// We provide a character based tokenizer and a word based one.
$characterTokenizer = new BasicCharacterTokenizer();
$characterTokenizer->tokenize("Hello", 2);
// Output: ["He", "el", "ll", "lo"]

// The word based tokenizer accepts a list of stopwords. Recommended:
// composer require voku/stop-words
// $stopWords = (new StopWords())->getStopWordsFromLanguage('en');
$wordTokenizer = new WordTokenizer(['list', 'of', 'stopwords']);
$wordTokenizer->tokenize("The quick brown fox", 2);
// Output: ["The quick", "quick brown", "brown fox"]

// Generating MinHash signatures of length 50. Basic hash functions are used,
// see PhpLsh\MinHash\BasicHash.
$minhash = MinHash::createBasicOfLength(50);
// Customizing the hashfunctions. The number of hash functions equals the length
// of the signature.
$minhash = new MinHash([$hashFunction1, $hashFunction1, ...])

// Putting it together: let's create an instance that uses word tokenization
// and short minhash signatures:
$lsh = new LSH(
    new WordTokenizer(['the', 'a', 'an', 'and']),
    MinHash::createBasicOfLength(20),
    new BasicHash()
);
```

Usage:

```php
public function findCandidateItems(
    // An iterable list of strings. Can be a simple array, but usually a
    // generator.
    iterable $inputs,
    // The number of bands and rows to split the minhash signatures in. Keep in
    // mind that b.r=n is a hard requirement, with n being the minhash signature
    // length.
    int $numberOfBands = 5,
    int $numberOfRows = 20,
    // How similar two items have to be in order to be considered candidates.
    // You should optimize t ~ (1/b)^(1/r) as explained in section 1.
    float $treshold = 0.8,
    // The shingle size used when tokenizing the input strings.
    int $shingleSize = 5,
): array;
```

## Example

Take a look at the [`example directory`](./example) to see how this library
can be used to extract similar Wikipedia titles.

## Tests

Run the test suite:

```console
vendor/bin/phpunit
```
