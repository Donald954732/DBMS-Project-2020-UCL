SELECT *
FROM
  (
    SELECT
      a.AuctionID,
      a.ItemName,
      a.StartingPrice,
      MAX(b.BidPrice) AS 'bidPrice',
      IF(
        MAX(bidPrice) IS NULL,
        a.StartingPrice,
        MAX(bidPrice)
      ) AS 'CurrentPrice'
    FROM
      auctions a
      LEFT JOIN bids b ON a.AuctionID = b.AuctionID
    WHERE
      (a.EndingTime - CURRENT_TIMESTAMP) > 0
      AND a.AuctionID IN (
        SELECT
          AuctionID
        FROM
          bids
        WHERE
          UserName = 'sheepishMallard0'
        GROUP BY
          AuctionID
      )
    GROUP BY
      a.AuctionID,
      a.ItemName,
      a.ItemDescription,
      a.StartingPrice,
      a.EndingTime
  ) MaxPriceGlobal
  INNER JOIN (
    SELECT
      AuctionID,
      MAX(BidPrice) AS UserMax
    FROM
      bids
    WHERE
      UserName = 'sheepishMallard0'
    GROUP BY
      AuctionID
  ) MaxPriceUser ON MaxPriceGlobal.AuctionID = MaxPriceUser.AuctionID
WHERE
  MaxPriceGlobal.CurrentPrice != MaxPriceUser.UserMax;
