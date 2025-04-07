<?php

interface RequiredToDoTaskData
{
    const string ID = 'id';
    const string TEXT = 'text';
    const string STATE = 'checked';
    const array REQUIRED_TO_DO_TASK_DATA_KEYS = [self::ID, self::TEXT, self::STATE];
}
