<?php
//
//namespace Laravel\Infrastructure\Modulr;
//
//use JetBrains\PhpStorm\ArrayShape;
//use Laravel\Infrastructure\Http\HttpClientResponse;
//
//class CardModulrService extends BaseModulrService
//{
//
//    protected function setResourcePath(): string
//    {
//        return "";
//    }
//
//    public function createPhysicalCard(string $accountId, CreatePhysicalCardDTO $createPhysicalCardDTO): HttpClientResponse
//    {
//        $cardPayload = $this->buildPhysicalCardPayload($createPhysicalCardDTO);
//        return $this->preCall()->setPhysicalCardPath($accountId)->post($cardPayload);
//    }
//
//    public function cancelCard(CancelCardDTO $cancelCardDTO): HttpClientResponse
//    {
//        return $this->preCall()->setCardPath($cancelCardDTO->cardId)->post($cancelCardDTO->toArray());
//    }
//
//    public function updateCardLimit(UpdateCardLimitDTO $updateCardLimitDTO): HttpClientResponse
//    {
//        return $this->preCall()->setCardPath($updateCardLimitDTO->cardId)->post($updateCardLimitDTO->toArray());
//    }
//
//    protected function setAccountPath(string $accountId): self
//    {
//        $this->httpClientService->setURLPaths(["accounts", $accountId]);
//        return $this;
//    }
//
//    protected function setPhysicalCardPath(string $accountId): CardModulrService
//    {
//        $this->setAccountPath($accountId)->httpClientService->setURLPath("physical-cards");
//        return $this;
//    }
//
//    protected function setCardPath(string $cardId): self
//    {
//        $this->httpClientService->setURLPaths(["cards", $cardId]);
//        return $this;
//    }
//
//    #[ArrayShape(["json" => "array"])]
//    protected function buildPhysicalCardPayload(CreatePhysicalCardDTO $createPhysicalCardDTO): array
//    {
//        return [
//            "json" => [
//                "authentication" => [
//                    "knowledgeBase" => [
//                        "type" => "FAVOURITE_CHILDHOOD_FRIEND",
//                        "answer" => $createPhysicalCardDTO->kbAnswer
//                    ]
//                ],
//                "design" => [
//                    "cardRef" => $createPhysicalCardDTO->cardRef,
//                    "packagingRef" => $createPhysicalCardDTO->packagingCardRef
//                ],
//                "holder" => [
//                    "billingAddress" => [
//                        "addressLine1" => $createPhysicalCardDTO->addressLine1,
//                        "addressLine2" => $createPhysicalCardDTO->addressLine2,
//                        "country" => $createPhysicalCardDTO->country,
//                        "postCode" => $createPhysicalCardDTO->postCode,
//                        "postTown" => $createPhysicalCardDTO->postTown
//                    ],
//                    "firstName" => $createPhysicalCardDTO->fname,
//                    "lastName" => $createPhysicalCardDTO->lname,
//                    "dateOfBirth" => $createPhysicalCardDTO->dateOfBirth,
//                    "mobileNumber" => $createPhysicalCardDTO->mobileNo,
//                    "email" => $createPhysicalCardDTO->email,
//                ],
//                "externalRef" => $createPhysicalCardDTO->externalRef,
//                "productCode" => $createPhysicalCardDTO->productCode,
//                "expiry" => $createPhysicalCardDTO->expiry,
//                "printedName" => $createPhysicalCardDTO->printedName,
//                "limit" => $createPhysicalCardDTO->cardLimit
//            ]
//        ];
//    }
//}
