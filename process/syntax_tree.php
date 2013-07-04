<?php
	/**
	 * tree multidimensional array
	 * @var array
	 *      Lists all the regular expressions for tokenization.
	 */
	$tree = array(
	'regex' => '/(.*)/',
	array(
		'regex' => '/(.*)/',
		array(
			'regex' => '/(.*)/',
			array(
				'regex' => '/^={3}$/',
				'token' => 'T_IDENT'
			),
			array(
				'regex' => '/^={2}$/',
				'token' => 'T_COMP'
			),
			array(
				'regex' => '/^={1}$/',
				'token' => 'T_EQUALS'
			),
			array(
				'regex' => '/^!=$/',
				'token' => 'T_NOT_EQUAL'
			),
			array(
				'regex' => '/^!==$/',
				'token' => 'T_NOT_IDENT'
			),
			array(
				'regex' => '/^<$/',
				'token' => 'T_LESS_THAN'
			),
			array(
				'regex' => '/^<=$/',
				'token' => 'T_LESS_THAN_EQUAL'
			),
			array(
				'regex' => '/^>$/',
				'token' => 'T_GRT_THAN'
			),
			array(
				'regex' => '/^>=$/',
				'token' => 'T_GRT_THAN_EQUAL'
			),
			array(
				'regex' => '/^\*=$/',
				'token' => 'T_MULTI_EQUAL'
			),
			array(
				'regex' => '/^\*$/',
				'token' => 'T_MULTI'
			),
			array(
				'regex' => '/^\+$/',
				'token' => 'T_ADD'
			),
			array(
				'regex' => '/^-$/',
				'token' => 'T_SUB'
			),
			array(
				'regex' => '/^\/$/',
				'token' => 'T_DIV'
			),
			array(
				'regex' => '/^\/=$/',
				'token' => 'T_DIV_EQUAL'
			),
			array(
				'regex' => '/^\+=$/',
				'token' => 'T_ADD_EQUAL'
			),
			array(
				'regex' => '/^-=$/',
				'token' => 'T_SUB_EQUAL'
			),
			array(
				'regex' => '/^(mod|%)$/',
				'token' => 'T_MOD'
			),
			array(
				'regex' => '/^%=$/',
				'token' => 'T_MOD_EQUAL'
			),
			array(
				'regex' => '/^\.=$/',
				'token' => 'T_CONCAT_EQUAL'
			)
		),
		array(
			'regex' => '/^(?i:Variable)\[(\'|\")(.*)(\'|\")\]$/',
			array(
				'regex' => '/^(?i:Variable\[(\'|\")(?<data>.*)(\'|\")\])$/',
				'token' => 'T_VAR'
			)
		),
		array(
			'regex' => '/^(?i:(foreach|while|for|endwhile|endforeach|endfor))$/',
			array(
				'regex' => '/^(?i:For)$/',
				'token' => 'T_FOR_START'
			),
			array(
				'regex' => '/(?i:Endfor)/',
				'token' => 'T_CLOSE_BLOCK'
			),
			array(
				'regex' => '/^(?i:while)$/',
				'token' => 'T_WHILE_START'
			),
			array(
				'regex' => '/^(?i:endwhile)$/',
				'token' => 'T_CLOSE_BLOCK'
			),
			array(
				'regex' => '/^(?i:foreach)$/',
				'token' => '/T_FOREACH_START'
			),
			array(
				'regex' => '/(?i:Endforeach)/',
				'token' => 'T_CLOSE_BLOCK'
			)
		),
		array(
			'regex' => '/^(?i:(if|then|else|elseif|endif))$/',
			array(
				'regex' => '/^(?i:if)$/',
				'token' => 'T_IF_START'
			),
			array(
				'regex' => '/^(?i:then)$/',
				'token' => 'T_OPEN_BLOCK'
			),
			array(
				'regex' => '/^(?i:endif)$/',
				'token' => 'T_CLOSE_BLOCK'
			),
			array(
				'regex' => '/^(?i:elseif)$/',
				'token' => 'T_ELIF_START'
			),
			array(
				'regex' => '/^(?i:else)$/',
				'token' => 'T_ELSE_START'
			)
		),
		array(
			'regex' => '/^(?i:(true|false))$/',
			array(
				'regex' => '/^(?i:true)/',
				'token' => 'T_TRUE'
			),
			array(
				'regex' => '/^(?i:false)/',
				'token' => 'T_FALSE'
			)
		),
		array(
			'regex' => '/(.*)/',
			array(
				'regex' => '/output/',
				'token' => 'T_PRINT'
			),
			array(
				'regex' => '/^\"(?<data>.*)\"$/',
				'token' => 'T_STR'
			),
			array(
				'regex' => '/^(?<data>^\-?([0-9]*[\.]?)?[0-9]*[E(\-|\+)?[\d]*]?$)$/',
				'token' => 'T_NUM'
			),
			array(
				'regex' => '/^\.$/',
				'token' => 'T_CONCAT'
			),
			array(
				'regex' => '/^inc$/',
				'token' => 'T_INCR'
			),
			array(
				'regex' => '/^dec$/',
				'token' => 'T_DECR'
			),
			array(
				'regex' => '/^;$/',
				'token' => 'T_SEPERATOR'
			)
		)
	)
);
?>