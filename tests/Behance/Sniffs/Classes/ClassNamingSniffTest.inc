<?php
class Behance_Sniffs_Classes_ClassNamingSniffTest extends Something {}
class Behance_Sniffs_Classes_ClassNamingSniffTestBad extends Something {}

// special cases for controllers - needs to be addressed at some point
class ClassNamingSniffTestBad extends badly_named_controller {}
class ClassNamingSniffTest extends badly_named_controller {}
