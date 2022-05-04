# 1: Manage Candidates

## Registering

### Regular

```php
$Election->addCandidate ( [CondorcetPHP\Condorcet\Candidate|string|null candidate = null] ): CondorcetPHP\Condorcet\Candidate
```
[>>>>>>> Method Reference](https://github.com/julien-boudry/Condorcet/blob/master/Documentation/Election%20Class/public%20Election--addCandidate.md)  


```php
use CondorcetPHP\Condorcet\Candidate;

$election->addCandidate('Wagner') ; // mb_strlen(Candidate Name) <= Election::MAX_LENGTH_CANDIDATE_ID, Default: 30

$myAutoCandidate = $election->addCandidate() ; // Empty argument will return an candidate object with an automatic name for you (From A to ZZZZZ)  

$election->addCandidate(2) ; // If you use integer, he will be converted to string (= '2')

$election->addCandidate(new Candidate ('Edgard Varèse')) ;
```
### Add multiple candidates from string or text file

#### Syntax
```
Candidate1
Candidate2 # You can add optionnal comments
Candidate3 ; Candidate4 # Or in the same line separated by ; with or without space (will be trim)
Candidate5
``` 

#### Method
```php
$election->parseCandidates('data/candidates.txt'); // Path to text file. Absolute or relative.
$election->parseCandidates($my_big_string); // Just my big string.
```

### Add multiple candidates from Json

#### Syntax
```php
json_encode( array(
	'CandidateName1',
	'CandidateName2'
) );
``` 

#### Method
```php
$election->addCandidatesFromJson($my_json_string);
```

## Removing
```php
$Election->removeCandidates ( CondorcetPHP\Condorcet\Candidate|array|string candidates_input ): array
```
[>>>>>>> Method Reference](https://github.com/julien-boudry/Condorcet/blob/master/Documentation/Election%20Class/public%20Election--removeCandidates.md)  


```php
$election->removeCandidates('Wagner') ;
$election->removeCandidates($myCandidateObject); // Not destroying your Candidate object. But just unlink it from this Election.
```


## Verify the Candidates list


```php
$election->getCandidatesList(); // Will return an array with all Candidate object.
$election->getCandidatesListAsString(); // Will return an array with all candidate name as string.
```

_Note: When you start voting, you will never be able to edit the candidate's list._  