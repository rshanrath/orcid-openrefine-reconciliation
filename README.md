#Demo ORCID Reconciliation Service for OpenRefine

Very basic demo of an [OpenRefine reconcilation service](https://github.com/OpenRefine/OpenRefine/wiki/Reconciliation-Service-API) that can be used to attempt retrieve ORCID identifiers based on a person's name or other values via the [ORCID Public Search API](http://support.orcid.org/knowledgebase/articles/132354-searching-with-the-public-api).

After matching, a new column with the ORCID identifier can be created by adding a new column based on the matched column using the expresion `cell.recon.match.id`

