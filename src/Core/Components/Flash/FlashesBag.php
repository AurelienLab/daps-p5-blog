<?php

namespace App\Core\Components\Flash;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Collection of messages displayed and consumed in views
 * Based on sessions
 */
class FlashesBag implements \Iterator
{

    const SESSION_DATA_NAME = '_app.flashes';

    private array $flashes = [];
    private Session $session;

    public function __construct(Request $request)
    {
        $this->session = $request->getSession();

        $flashesInSession = $this->session->get(self::SESSION_DATA_NAME, []);

        foreach ($flashesInSession as $flash) {
            $flash = new FlashMessage($flash['type'], $flash['message']);
            $this->addFlash($flash);
        }
    }

    /**
     * Add a flash message to the collection
     *
     * @param FlashMessage $flashMessage
     *
     * @return void
     */
    public function addFlash(FlashMessage $flashMessage)
    {
        $this->flashes[] = $flashMessage;
    }

    /**
     * Update session with current flashMessages
     *
     * @return void
     */
    public function saveToSession()
    {
        $data = [];

        foreach ($this->flashes as $flash) {
            if ($flash === null) {
                continue;
            }
            $data[] = [
                'type' => $flash->getType(),
                'message' => $flash->getMessage(),
            ];
        }

        $this->session->remove(self::SESSION_DATA_NAME);
        $this->session->set(self::SESSION_DATA_NAME, $data);
        $this->session->save();
    }

    // ITERATION METHODS

    public function rewind(): void
    {
        reset($this->flashes);
    }

    public function current(): mixed
    {
        $flash = current($this->flashes);
        $this->flashes[$this->key()] = null;
        $this->saveToSession();
        return $flash;
    }

    public function key(): string|int|null
    {
        return key($this->flashes);
    }

    public function next(): void
    {
        next($this->flashes);
    }

    public function valid(): bool
    {
        return key($this->flashes) !== null;
    }
}
