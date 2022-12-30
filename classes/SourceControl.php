<?php
namespace Utopigs\Pigsync\Classes;

use BackendAuth;
use Github\Client;
use Utopigs\Pigsync\Models\Commit;
use Utopigs\Pigsync\Models\Change;

class SourceControl
{
    private $team;
    private $branch;
    private $repository;
    private $token;
    private $changes = [];

    const AUTHOR_ID = 'author_id';
    const AUTHOR_NAME = 'author_name';
    const AUTHOR_EMAIL = 'author_email';

    public function __construct($team, $repository, $branch, $token)
    {
        $this->team = $team;
        $this->branch = $branch;
        $this->repository = $repository;
        $this->token = $token;
    }

    public function author($format)
    {
        $user = BackendAuth::getUser();

        switch ($format) {
            case static::AUTHOR_ID:
                return $user->id;

            case static::AUTHOR_NAME:
                return $user->full_name;

            case static::AUTHOR_EMAIL:
                return $user->email;

        }

        return null;
    }

    public function add($filename, $contents)
    {
        $change = new Change();
        $change->basename = basename($filename);
        $change->filename = $filename;
        $change->payload = $contents;
        $change->user_id = $this->author(static::AUTHOR_ID);
        $change->checksum = hash('sha256', $contents);
        $change->save();

        $this->changes[] = $change;

        return $change;
    }

    public function commit($message)
    {
        $commit = new Commit();
        $commit->author_name = $this->author(static::AUTHOR_NAME);
        $commit->author_email = $this->author(static::AUTHOR_EMAIL);
        $commit->message = $message;
        $commit->save();

        while (count($this->changes) > 0) {
            $change = array_shift($this->changes);
            $change->commit_id = $commit->id;
            $change->save();
        }

        return $commit;
    }

    public function status()
    {
        return Commit::select()
            ->where('processed', 0)
            ->orderBy('created_at', 'asc')
            ->get();
    }

    public function push()
    {
        $base_path = base_path();
        $commits = $this->status();
        $client = new Client();
        $client->authenticate($this->token, null, Client::AUTH_ACCESS_TOKEN);

        foreach ($commits as $commit) {
            foreach ($commit->changes as $change) {
                $filename = substr($change->filename, strlen($base_path)+1);
                $oldFile = $client->api('repo')->contents()->show(
                    $this->team,
                    $this->repository,
                    $filename,
                    $this->branch,
                );
                $client->api('repo')->contents()->update(
                    $this->team,
                    $this->repository,
                    $filename,
                    $change->payload,
                    $commit->message,
                    $oldFile['sha'],
                    $this->branch,
                    [
                        'name' => $commit->author_name,
                        'email' => $commit->author_email
                    ]
                );
            }

            $commit->processed = 1;
            $commit->save();
        }

        return $commits;
    }
}
