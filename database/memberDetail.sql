USE [mobileBanking]
GO

/****** Object:  Table [dbo].[memberDetails]    Script Date: 05/02/2014 08:32:36 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

CREATE TABLE [dbo].[memberDetails](
	[registrationCode] [nvarchar](50) NULL,
	[registrationDate] [datetime] NULL,
	[idNumber] [nvarchar](50) NULL,
	[fullNames] [nvarchar](100) NULL,
	[gender] [nchar](10) NULL,
	[subCounty] [nvarchar](50) NULL
) ON [PRIMARY]

GO

