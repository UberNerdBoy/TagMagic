<?php

/**

Algorithm for calculating the weight of individual words.

Note that this algorithm should only be applied after running
all necessary filtering methods to remove common/meaningless words
as well as punctuation and code blocks

W = weight
C = count
E = boolean (0 or 1) - If word is in excerpt
T = boolean (0 or 1) - If word is in title
L = length of word

W = C + L + (T*L) + (E*L) / C

*/

#Load up additional functions
require_once('inc/funcs.php');

#Grab post contents
$content = strtolower(file_get_contents('content.txt'));

#Grab post excerpt
$excerpt = strtolower(file_get_contents('excerpt.txt'));

#Grab post title
$title = strtolower(file_get_contents('title.txt'));

#Set minimum length of meaningful words
#Set recurrence flag to indicate popularity
$min_length = $popularity_flag = 3;

#Remove content within <div> tags (as it's normally code)
#Make sure that you're only grabbing inner post contents from file
#Just like what WordPress stores in the database, no wrapper <div>
#If you grab the surrounding <div>, the entire contents will be removed
$content = preg_replace("/<div.+?>.+<\/div>/ims", " ", $content);

#Remove HTML comments
$content = preg_replace("/<!--.+>/ims", " ", $content);

#Remove file names
$content = preg_replace("/[a-zA-Z0-9\-_]+\.[a-zA-Z]{2,3}/ims", " ", $content);

#Remove ASCII codes
$content = preg_replace("/&[^\s]+?;/ims", " ", $content);
$excerpt = preg_replace("/&[^\s]+?;/ims", " ", $excerpt);
$title   = preg_replace("/&[^\s]+?;/ims", " ", $title);

#Remove single quotes (but preserve apostrophes)
$content = preg_replace("/(?<= )'|'(?= )|'(?=\W)|(?=\W)'/", '', $content);
$excerpt = preg_replace("/(?<= )'|'(?= )|'(?=\W)|(?=\W)'/", '', $excerpt);
$title   = preg_replace("/(?<= )'|'(?= )|'(?=\W)|(?=\W)'/", '', $title);

#Remove reamining HTML tags
$content = strip_tags($content);

#Remove punctuation
$content = preg_replace("/[^\w\s\-\']/", '', $content);
$excerpt = preg_replace("/[^\w\s\-\']/", '', $excerpt);
$title   = preg_replace("/[^\w\s\-\']/", '', $title);

#Remove 2 or more consecutive whitespaces
$content = preg_replace("/\s\s+/", ' ', $content);
$excerpt = preg_replace("/\s\s+/", ' ', $excerpt);
$title   = preg_replace("/\s\s+/", ' ', $title);

#Create array of common words
#I'll admit, I did have to add in particular use-case words to this list to get my desired results, 
#but I think with some work, the list could be improved to suite all needs/post types
$common_words = array(
  "aren't", "does", "doesn't", "this", "very", "much", "from", "doing", "he's", "he'd", 
  "he'll", "hers", "she's", "she'd", "she'll", "around", "they", "them", "those", "force",
  "their", "ours", "mine", "yours", "theirs", "you're", "you'll", "they'd", "they'll", "want",
  "you'd", "i'll", "they're", "we're", "we'll", "that", "some", "most", "part", "else", "when", 
  "while", "don't", "didn't", "will", "won't", "weren't", "wasn't", "have", "haven't", "can't", 
  "never", "always", "sometimes", "maybe", "then", "call", "calls", "called", "than", "when", 
  "where", "into", "outside", "lets", "being", "bought", "buying", "love", "hate", "submit"
);

#Create arrays of title/excerpt words
$title_words   = explode(' ', $title);
$excerpt_words = explode(' ', $excerpt);

#Create array of words from content
$words = explode(' ', $content);

#Loop through words array to count occurences
foreach($words as $word) {

  #Trim excess whitespace
  $word = trim($word);

  #Set the length of the word
  $L = strlen($word);

  #Check for common words
  #Make sure word isn't empty
  #Check that word is longer than $min_length
  if(!in_array($word, $common_words) && !emptyVal($word) && $L > $min_length) {

    #Set the number of occurrences
    $C = substr_count($content, $word);

    #Set $T and $E to 0
    $T = $E = 0;

    #Make sure word is considered popular
    if($C > $popularity_flag) {

      #Create values for $T and $E
      switch($word) {
        case in_array($word, $title_words):
          $T = 1;
        break;
        case in_array($word, $excerpt_words):
          $E = 1;
        break;
        default:
          $T = 0;
          $E = 0;
        break;
      }

      #Create the array of results
      $list[$word]['count'] = $C;
      $list[$word]['length'] = $L;
      $list[$word]['in_title'] = $T;
      $list[$word]['in_excerpt'] = $E;
      $list[$word]['weight'] = round( ( ($C + $L) + ( ($T*$L) + ($E*$L) ) ) / $C, 1);
    }
  }
}

#Print the results
echo "<pre>";
print_r(array_slice(easy_sort($list, 'weight', SORT_DESC),0,5));
echo "</pre>";