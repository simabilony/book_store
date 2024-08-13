<?php

namespace App\Service;

use App\Models\Author;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Exception;

class AuthorService
{
public function listAuthor($per_page=10 ,$page=1)
{
    try {
        return Author::select(['id','name'])->paginate($per_page);
    }
      catch (Exception $e){
        Log::error("error listing Author", (array)$e->getMessage());
      throw new \Exception(ApiResponseService::error("error listing data"));
      }
}
    public function createAuthor(array $data)
    {
        try {
            // Create a new author record with the provided data
            return Author::create([
                'name'=> $data['name'],
                'biography'=> $data['biography'],
            ]);
        } catch (Exception $e) {
            Log::error('Error creating author: ' . $e->getMessage());
            throw new \Exception("Error creating author.");
        }
    }
    /**
     * Get the details of a specific author by its ID.
     *
     * @param int $id
     * @return \App\Models\Author
     */


    /**
     * Get the details of a specific author by its ID.
     *
     * @param int $id
     * @return \App\Models\Author
     */
    public function getAuthor(int $id)
    {
        try {
            // Find the author by ID or fail with a 404 error if not found
            return Author::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Log::error('Author not found: ' . $e->getMessage());
            throw new \Exception('Author not found.');
        } catch (Exception $e) {
            Log::error('Error retrieving author: ' . $e->getMessage());
            throw new \Exception('Error retrieving author.');
        }
    }

    /**
     * Update the details of a specific author.
     *
     * @param array $data
     * @param int $id
     * @return \App\Models\Author
     */
    public function updateAuthor(array $data, int $id)
    {
        try {
            // Find the author by ID or fail with a 404 error if not found
            $author = Author::findOrFail($id);

            // Update the author with the provided data, filtering out null values
            $author->update(array_filter($data));

            // Return the updated author
            return $author;
        } catch (ModelNotFoundException $e) {
            Log::error('Author not found: ' . $e->getMessage());
            throw new \Exception('Author not found.');
        } catch (Exception $e) {
            Log::error('Error updating author: ' . $e->getMessage());
            throw new \Exception('Error updating author.');
        }
    }

    /**
     * Delete a specific author by its ID.
     *
     * @param int $id
     * @return void
     */
    public function deleteAuthor(int $id)
    {
        try {
            // Find the author by ID or fail with a 404 error if not found
            $author = Author::findOrFail($id);

            // Delete the author
            $author->delete();
        } catch (ModelNotFoundException $e) {
            Log::error('Author not found: ' . $e->getMessage());
            throw new \Exception('Author not found.');
        } catch (Exception $e) {
            Log::error('Error deleting author: ' . $e->getMessage());
            throw new \Exception('Error deleting author.');
        }
    }

}
