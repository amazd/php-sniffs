<?php
class Behance_Sniffs_Classes_ClassNamingSniffTest extends SomeCrap {}
class Behance_Sniffs_Classes_ClassNamingSniffTestBad extends SomeCrap {}

// special cases for controllers - needs to be addressed at some point
class ClassNamingSniffTestBad extends badly_named_controller_crap {}
class ClassNamingSniffTest extends badly_named_controller_crap {}
