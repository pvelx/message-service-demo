<?php


namespace App\Doctrine;


trait StateMachineExtension
{
    /**
     * @param array $stateMachine
     * @param $currentState
     * @param $newState
     * @return bool
     */
    private function isValidChangeState(array $stateMachine, $currentState, $newState)
    {
        if (isset($stateMachine[$currentState])) {
            if (isset($stateMachine[$currentState][$newState])) {
                return true;
            }
        } else {
            foreach ($stateMachine as $status => $state) {
                if (is_array($state)) {
                    $this->isValidChangeState($state, $currentState, $newState);
                }
            }
        }
        return false;
    }
}
