<?php

namespace ProKerja\Job;

enum StatusEnum: string
{

    case PENDING = "pending";
    case PROCESSING = "processing";
    case COMPLETED = "completed";
    case FAILED = "failed";


}
