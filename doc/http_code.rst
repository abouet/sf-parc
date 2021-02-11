#200 | OK                     | Succès de réponse pour toutes méthodes sauf la création avec POST.
#201 | Created                | Réponse à un POST qui crée une ressource. Doit être obligatoirement accompagné du header Location avec le lien vers la nouvelle ressource. On peut embarquer la nouvelle ressource pour éviter au client de refaire une nouvelle requête.
#204 | No Content             | Une requête réussie qui ne renvoie aucun contenu (comme un DELETE par exemple).
#304 | Not Modified           | Utilisé avec la gestion d'un cache.
#400 | Bad Request            | La requête a échouée car le contenu ne peut pas être compris (un contenu qui ne peut être parsé par exemple).
#401 | Unauthorized           | Lorsqu'une authentification a échouée. Utile pour afficher une popup quand l'authentification se fait par un navigateur.
#403 | Forbidden              | L'authentification est correcte mais l'utilisateur ne peut pas accéder à la ressource.
#404 | Not Found              | La ressource n'a pas pu être trouvée.
#405 | Method Not Allowed     | Quand une méthode non autorisée a été utilisée par l'utilisateur.
#410 | Gone                   | La ressource n'est plus disponible. Utile pour les vieilles versions de l'API.
#415 | Unsupported Media Type | Si le Content-Type est incorrect.
#422 | Unprocessable Entity   | Utilisé pour les erreurs de validation.
#429 | Too Many Requests      | Utilisé lorsque la limite de requêtes autorisées a été dépassée.
#500 | Internal Error         | Un message générique pour indiquer qu'il y a eu un problème mais qu'il ne vient pas de l'utilisateur.
#503 | Service Unavailable    | Le service est down ou en maintenance. Status temporaire, en général.
