<?php

namespace App;

enum PlayerTips: string
{
    case NO_TIP = 'Nothing to suggest';
    case WARNING = 'Your rival can win';
    case MATCH_CLOSED = 'Your rival will win anyway';
}
